<?php

namespace Bitrix\Sale\PaySystem;

use Bitrix\Main\Entity\EntityError;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\NotSupportedException;
use Bitrix\Main\Request;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Order;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Result;
use Bitrix\Main\IO;
use Bitrix\Sale\ResultError;

Loc::loadMessages(__FILE__);

class Service
{
	/** @var ServiceHandler|IHold|IRefund|IPrePayable|ICheckable|IPayable $handler */
	private $handler = null;
	/**
	 * @var array
	 */
	private $fields = array();

	/**
	 * @param $fields
	 */
	public function __construct($fields)
	{
		$handlerType = '';
		$className = '';

		$name = Manager::getFolderFromClassName($fields['ACTION_FILE']);

		foreach (Manager::getHandlerDirectories() as $type => $path)
		{
			if (IO\File::isFileExists($_SERVER['DOCUMENT_ROOT'].$path.$name.'/handler.php'))
			{
				$className = Manager::getClassNameFromPath($fields['ACTION_FILE']);
				if (!class_exists($className))
					require_once($_SERVER['DOCUMENT_ROOT'].$path.$name.'/handler.php');

				if (class_exists($className))
				{
					$handlerType = $type;
					break;
				}
			}
		}

		if ($className === '')
		{
			$className = '\Bitrix\Sale\PaySystem\CompatibilityHandler';
			$handlerType = $fields['ACTION_FILE'];
		}

		$this->fields = $fields;
		$this->handler = new $className($handlerType, $this);
	}

	/**
	 * @param Payment $payment
	 * @param Request|null $request
	 * @return mixed
	 */
	public function initiatePay(Payment $payment, Request $request = null)
	{
		return $this->handler->initiatePay($payment, $request);
	}

	/**
	 * @return bool
	 */
	public function isRefundable()
	{
		return $this->handler instanceof IRefund;
	}

	/**
	 * @param Payment $payment
	 * @param int $refundableSum
	 * @return ServiceResult|Result
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 */
	public function refund(Payment $payment, $refundableSum = 0)
	{
		if ($this->isRefundable())
		{
			$result = new Result();

			if (!$payment->isPaid())
			{
				$result->addError(new ResultError(Loc::getMessage('SALE_PS_SERVICE_PAYMENT_NOT_PAID')));
				return $result;
			}

			if ($refundableSum == 0)
				$refundableSum = $payment->getSum();

			/** @var ServiceResult $result */
			$result = $this->handler->refund($payment, $refundableSum);

			return $result;
		}

		throw new SystemException();
	}

	/**
	 * @param Request $request
	 * @return Result
	 * @throws \Bitrix\Main\ArgumentNullException
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 * @throws \Bitrix\Main\ObjectNotFoundException
	 */
	public function processRequest(Request $request)
	{
		$processResult = new Result();

		$paymentId = $this->handler->getPaymentIdFromRequest($request);

		if (empty($paymentId))
		{
			$errorMessage = str_replace('#PAYMENT_ID#', $paymentId, Loc::getMessage('SALE_PS_SERVICE_PAYMENT_ERROR'));
			$processResult->addError(new Error($errorMessage));
			ErrorLog::add(array(
				'ACTION' => 'processRequest',
				'MESSAGE' => $errorMessage
			));
			return $processResult;
		}

		list($orderId, $paymentId) = Manager::getIdsByPayment($paymentId);

		if (!$orderId)
		{
			$errorMessage = str_replace('#ORDER_ID#', $orderId, Loc::getMessage('SALE_PS_SERVICE_ORDER_ERROR'));
			$processResult->addError(new Error($errorMessage));
			ErrorLog::add(array(
				'ACTION' => 'processRequest',
				'MESSAGE' => $errorMessage
			));
			return $processResult;
		}

		/** @var \Bitrix\Sale\Order $order */
		$order = Order::load($orderId);

		if (!$order)
		{
			$errorMessage = str_replace('#ORDER_ID#', $orderId, Loc::getMessage('SALE_PS_SERVICE_ORDER_ERROR'));
			$processResult->addError(new Error($errorMessage));
			ErrorLog::add(array(
				'ACTION' => 'processRequest',
				'MESSAGE' => $errorMessage
			));
			return $processResult;
		}

		if ($order->isCanceled())
		{
			$errorMessage = str_replace('#ORDER_ID#', $orderId, Loc::getMessage('SALE_PS_SERVICE_ORDER_CANCELED'));
			$processResult->addError(new Error($errorMessage));
			ErrorLog::add(array(
				'ACTION' => 'processRequest',
				'MESSAGE' => $errorMessage
			));
			return $processResult;
		}
		/** @var \Bitrix\Sale\PaymentCollection $collection */
		$collection = $order->getPaymentCollection();

		/** @var \Bitrix\Sale\Payment $payment */
		$payment = $collection->getItemById($paymentId);

		if (!$payment)
		{
			$errorMessage = str_replace('#PAYMENT_ID#', $orderId, Loc::getMessage('SALE_PS_SERVICE_PAYMENT_ERROR'));
			$processResult->addError(new Error($errorMessage));
			ErrorLog::add(array(
				'ACTION' => 'processRequest',
				'MESSAGE' => $errorMessage
			));
			return $processResult;
		}

		if (ErrorLog::DEBUG_MODE)
		{
			ErrorLog::add(array(
				'ACTION' => 'RESPONSE',
				'MESSAGE' => print_r($request->toArray(), 1)
			));
		}

		/** @var \Bitrix\Sale\PaySystem\ServiceResult $serviceResult */
		$serviceResult = $this->handler->processRequest($payment, $request);

		if ($serviceResult->isSuccess())
		{
			$status = null;
			$operationType = $serviceResult->getOperationType();

			if ($operationType == ServiceResult::MONEY_COMING)
				$status = 'Y';
			else if ($operationType == ServiceResult::MONEY_LEAVING)
				$status = 'N';

			if ($status !== null)
			{
				$paidResult = $payment->setPaid($status);
				if (!$paidResult->isSuccess())
				{
					ErrorLog::add(array(
						'ACTION' => 'PAYMENT SET PAID',
						'MESSAGE' => join(' ', $paidResult->getErrorMessages())
					));
					$serviceResult->setResultApplied(false);
				}
			}

			$psData = $serviceResult->getPsData();
			if ($psData)
			{
				$res = $payment->setFields($psData);

				if (!$res->isSuccess())
				{
					ErrorLog::add(array(
						'ACTION' => 'PAYMENT SET DATA',
						'MESSAGE' => join(' ', $res->getErrorMessages())
					));
					$serviceResult->setResultApplied(false);
				}
			}

			$saveResult = $order->save();

			if (!$saveResult->isSuccess())
			{
				ErrorLog::add(array(
					'ACTION' => 'ORDER SAVE',
					'MESSAGE' => join(' ', $saveResult->getErrorMessages())
				));
				$serviceResult->setResultApplied(false);
			}
		}
		else
		{
			$serviceResult->setResultApplied(false);
		}

		$this->handler->sendResponse($serviceResult, $request);

		return $processResult;
	}

	/**
	 * @return string
	 */
	public function getConsumerName()
	{
		return 'PAYSYSTEM_'.$this->fields['ID'];
	}

	/**
	 * @return array
	 */
	public function getHandlerDescription()
	{
		return $this->handler->getDescription();
	}

	/**
	 * @return bool
	 */
	public function isBlockable()
	{
		return $this->handler instanceof IHold;
	}

	/**
	 * @param Payment $payment
	 * @return mixed
	 * @throws SystemException
	 */
	public function cancel(Payment $payment)
	{
		if ($this->isBlockable())
			return $this->handler->cancel($payment);

		throw new SystemException();
	}

	/**
	 * @param Payment $payment
	 * @return mixed
	 * @throws SystemException
	 */
	public function confirm(Payment $payment)
	{
		if ($this->isBlockable())
			return  $this->handler->confirm($payment);

		throw new SystemException();
	}

	/**
	 * @param $name
	 * @return mixed
	 */
	public function getField($name)
	{
		return $this->fields[$name];
	}

	/**
	 * @return array
	 */
	public function getCurrency()
	{
		return $this->handler->getCurrencyList();
	}

	/**
	 * @return bool
	 */
	public function isCash()
	{
		return $this->fields['IS_CASH'] == 'Y';
	}

	/**
	 * @param Payment $payment
	 * @return Result
	 */
	public function creditNoDemand(Payment $payment)
	{
		return $this->handler->creditNoDemand($payment);
	}

	/**
	 * @param Payment $payment
	 * @return Result
	 */
	public function debitNoDemand(Payment $payment)
	{
		return $this->handler->debitNoDemand($payment);
	}

	/**
	 * @return bool
	 */
	public function isPayable()
	{
		if ($this->handler instanceof IPayable)
			return true;

		if (method_exists($this->handler, 'isPayableCompatibility'))
			return $this->handler->isPayableCompatibility();

		return false;
	}

	/**
	 * @return bool
	 */
	public function isAffordPdf()
	{
		return $this->handler->isAffordPdf();
	}

	/**
	 * @param Payment $payment
	 * @return mixed
	 */
	public function getPaymentPrice(Payment $payment)
	{
		if ($this->isPayable())
			return $this->handler->getPrice($payment);

		return 0;
	}

	/**
	 * @param array $params
	 */
	public function setTemplateParams(array $params)
	{
		$this->handler->setExtraParams($params);
	}

	/**
	 * @param Payment|null $payment
	 * @param $templateName
	 */
	public function showTemplate(Payment $payment = null, $templateName)
	{
		$this->handler->showTemplate($payment, $templateName);
	}

	/**
	 * @return bool
	 */
	public function isPrePayable()
	{
		return $this->handler instanceof IPrePayable;
	}

	/**
	 * @param Payment|null $payment
	 * @param Request $request
	 * @throws NotSupportedException
	 */
	public function initPrePayment(Payment $payment = null, Request $request)
	{
		if ($this->isPrePayable())
			return $this->handler->initPrePayment($payment, $request);

		throw new NotSupportedException;
	}

	/**
	 * @return mixed
	 * @throws NotSupportedException
	 */
	public function getPrePaymentProps()
	{
		if ($this->isPrePayable())
			return $this->handler->getProps();

		throw new NotSupportedException;
	}

	/**
	 * @param array $orderData
	 * @return mixed
	 * @throws NotSupportedException
	 */
	public function basketButtonAction(array $orderData = array())
	{
		if ($this->isPrePayable())
			return $this->handler->basketButtonAction($orderData = array());

		throw new NotSupportedException;
	}

	/**
	 * @param array $orderData
	 * @return mixed
	 * @throws NotSupportedException
	 */
	public function setOrderDataForPrePayment($orderData = array())
	{
		if ($this->isPrePayable())
			return $this->handler->setOrderConfig($orderData);

		throw new NotSupportedException;
	}

	/**
	 * @param $orderData
	 * @return mixed
	 * @throws NotSupportedException
	 */
	public function payOrderByPrePayment($orderData)
	{
		if ($this->isPrePayable())
			return $this->handler->payOrder($orderData);

		throw new NotSupportedException;
	}

	/**
	 * @return array
	 */
	public function getFieldsValues()
	{
		return $this->fields;
	}

	/**
	 * @return bool
	 */
	public function isAllowEditPayment()
	{
		return $this->fields['ALLOW_EDIT_PAYMENT'] == 'Y';
	}

	/**
	 * @return bool
	 */
	public function isCheckable()
	{
		if ($this->handler instanceof ICheckable)
			return true;

		if (method_exists($this->handler, 'isCheckableCompatibility'))
			return $this->handler->isCheckableCompatibility();

		return true;
	}

	/**
	 * @param Payment $payment
	 * @return \Bitrix\Main\Entity\AddResult|\Bitrix\Main\Entity\UpdateResult|ServiceResult|Result|mixed
	 * @throws NotSupportedException
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 */
	public function check(Payment $payment)
	{
		if ($this->isCheckable())
		{
			/** @var \Bitrix\Sale\PaymentCollection $paymentCollection */
			$paymentCollection = $payment->getCollection();

			/** @var \Bitrix\Sale\Order $order */
			$order = $paymentCollection->getOrder();

			if (!$order->isCanceled())
			{
				/** @var ServiceResult $result */
				$result = $this->handler->check($payment);
				if ($result instanceof ServiceResult && $result->isSuccess())
				{
					$psData = $result->getPsData();
					if ($psData)
					{
						$res = $payment->setFields($psData);
						if (!$res->isSuccess())
							return $res;

						if ($result->getOperationType() == ServiceResult::MONEY_COMING)
						{
							$res = $payment->setPaid('Y');
							if (!$res->isSuccess())
								return $res;
						}

						$res = $order->save();
						if (!$res->isSuccess())
							return $res;
					}
				}
			}
			else
			{
				$result = new ServiceResult();
				$result->addError(new EntityError(Loc::getMessage('SALE_PS_SERVICE_ORDER_CANCELED', array('#ORDER_ID#' => $order->getId()))));
			}

			return $result;
		}

		throw new NotSupportedException;
	}
}