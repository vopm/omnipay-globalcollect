<?php

namespace Omnipay\GlobalCollect;

use Omnipay\Tests\GatewayTestCase;

/**
 * @property Gateway gateway
 */
class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->gateway->setTestMode(true);
        $this->gateway->setApiKeyId(getenv('GC_API_KEY'));
        $this->gateway->setApiMerchantId(getenv('GC_MERCHANT'));
        $this->gateway->setApiSecret(getenv('GC_API_SECRET'));
    }

    public function testRetrieve()
    {
        $this->setMockHttpResponse('payments/000000895910000023670000200001/retrieve.200.txt');

        $request = $this->gateway->fetchTransaction(['transactionReference' => '000000895910000023670000200001']);

        $this->assertInstanceOf('Omnipay\GlobalCollect\Message\Payments\PaymentRetrieveRequest', $request);

        $response = $request->send();

        $this->assertEquals('000000895910000023670000200001', $response->getTransactionReference());

        $this->assertTrue($response->isSuccessful());
    }

    public function testAuthorize()
    {
        $this->setMockHttpResponse('payments/000000895910000023670000200001/authorize.200.txt');

        $request = $this->gateway->authorize([
            'amount'   => 828.00,
            'currency' => 'DOP',
            'token'    => 'e9296584-f278-4eef-920e-6d8cf17a6c60',
            'clientIp' => '127.0.0.1',
            'card'     => [
                'firstName' => 'John',
                'lastName'  => 'Wick',
                'address1'  => 'NY Ave.',
                'city'      => 'New York',
                'state'      => 'NY',
                'postcode'      => '10103',
                'country'      => 'US',
                'phone'      => '8111111111',
                'email'      => 'me@example.com',
            ],
            'order'=>[
                'customerId'=>888888,
                'orderId'=>111111,
                'merchantReference'=>'f5f59512-aad3-1c44-298',
                'descriptor'=>'Test Descriptor',
            ]
        ]);

        $this->assertInstanceOf('Omnipay\GlobalCollect\Message\Payments\PaymentAuthorizeRequest', $request);

        $this->assertSame('828.00', $request->getAmount());

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
    }

    public function testCapture(){
        $this->setMockHttpResponse('payments/000000895910000023670000200001/capture.200.txt');

        $request = $this->gateway->capture([
            'transactionReference'=>'000000895910000023670000200001'
        ]);

        $this->assertInstanceOf('Omnipay\GlobalCollect\Message\Payments\PaymentCaptureRequest', $request);

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
    }

    public function testCaptureCancel(){
        $this->setMockHttpResponse('payments/000000895910000023670000200001/captureCancel.200.txt');

        $request = $this->gateway->captureCancel([
            'transactionReference'=>'000000895910000023670000200001'
        ]);

        $this->assertInstanceOf('Omnipay\GlobalCollect\Message\Payments\PaymentCaptureCancelRequest', $request);

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
    }

    public function testTokenize(){
        $this->setMockHttpResponse('payments/000000895910000023670000200001/tokenize.200.txt');

        $request = $this->gateway->tokenize([
            'transactionReference'=>'000000895910000023670000200001'
        ]);

        $this->assertInstanceOf('Omnipay\GlobalCollect\Message\Payments\PaymentTokenizeRequest', $request);

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
    }

    public function testVoid(){
        $this->setMockHttpResponse('payments/000000895910000023670000200001/cancel.200.txt');

        $request = $this->gateway->void([
            'transactionReference'=>'000000895910000023670000200001'
        ]);

        $this->assertInstanceOf('Omnipay\GlobalCollect\Message\Payments\PaymentVoidRequest', $request);

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
    }

    public function testRefund(){
        $this->setMockHttpResponse('payments/000000895910000023670000200001/refund.200.txt');

        $request = $this->gateway->refund([
            'transactionReference'=>'000000895910000023670000200001',
            'amount'=>200.00,
            'card'     => [
                'firstName' => 'John',
                'lastName'  => 'Wick',
                'address1'  => 'NY Ave.',
                'city'      => 'New York',
                'state'      => 'NY',
                'postcode'      => '10103',
                'country'      => 'US',
                'phone'      => '8111111111',
                'email'      => 'me@example.com',
            ]
        ]);

        $this->assertInstanceOf('Omnipay\GlobalCollect\Message\Payments\PaymentRefundRequest', $request);

        $response = $request->send();

        $this->assertTrue($response->isSuccessful());
    }

}
