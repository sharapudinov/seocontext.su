<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?

$entityId = CSalePaySystemAction::GetParamValue("ORDER_PAYMENT_ID");
list($orderId, $paymentId) = \Bitrix\Sale\PaySystem\Manager::getIdsByPayment($entityId);

/** @var \Bitrix\Sale\Order $order */
$order = \Bitrix\Sale\Order::load($orderId);

/** @var \Bitrix\Sale\PaymentCollection $paymentCollection */
$paymentCollection = $order->getPaymentCollection();

/** @var \Bitrix\Sale\Payment $payment */
$payment = $paymentCollection->getItemById($paymentId);

$data = \Bitrix\Sale\PaySystem\Manager::getById($payment->getPaymentSystemId());

$service = new \Bitrix\Sale\PaySystem\Service($data);
$service->initiatePay($payment);

return;

$ShopID = CSalePaySystemAction::GetParamValue("SHOP_ID");
$scid = CSalePaySystemAction::GetParamValue("SCID");
$orderNumber = CSalePaySystemAction::GetParamValue("ORDER_PAYMENT_ID");
$Sum = CSalePaySystemAction::GetParamValue("SHOULD_PAY");
$orderDate = CSalePaySystemAction::GetParamValue("ORDER_DATE");
$customerNumber = CSalePaySystemAction::GetParamValue("ORDER_ID");
$paymentType = CSalePaySystemAction::GetParamValue("PAYMENT_VALUE");

$Sum = number_format($Sum, 2, ',', '');
?>
<font class="tablebodytext">
Услугу предоставляет сервис онлайн-платежей <b>&laquo;Яндекс.Касса&raquo;</b>.<br /><br />
Сумма к оплате по счету: <b><?=$Sum?> р.</b><br />
<br />
</font>
<?if(strlen(CSalePaySystemAction::GetParamValue("IS_TEST")) > 0):
	?>
	<form name="ShopForm" action="https://demomoney.yandex.ru/eshop.xml" method="post" target="_blank">
<?else:
	?>
	<form name="ShopForm" action="https://money.yandex.ru/eshop.xml" method="post">
<?endif;?>
<font class="tablebodytext">
<input name="ShopID" value="<?=$ShopID?>" type="hidden">
<input name="scid" value="<?=$scid?>" type="hidden">
<input name="customerNumber" value="<?=$customerNumber?>" type="hidden">
<input name="orderNumber" value="<?=$orderNumber?>" type="hidden">
<input name="Sum" value="<?=$Sum?>" type="hidden">
<input name="paymentType" value="<?=$paymentType?>" type="hidden">
<input name="cms_name" value="1C-Bitrix" type="hidden">

<!-- <br /> -->
<!-- Детали заказа:<br /> -->
<!-- <input name="OrderDetails" value="заказ №<?=$orderNumber?> (<?=$orderDate?>)" type="hidden"> -->
<br />
<input name="BuyButton" value="Оплатить" type="submit">

</font><p><font class="tablebodytext"><b>Обратите внимание:</b> если вы откажетесь от покупки, для возврата денег вам придется обратиться в магазин.</font></p>
</form>