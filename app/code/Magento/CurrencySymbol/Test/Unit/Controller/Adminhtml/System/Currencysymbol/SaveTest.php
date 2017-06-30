<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CurrencySymbol\Test\Unit\Controller\Adminhtml\System\Currencysymbol;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class SaveTest
 */
class SaveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CurrencySymbol\Controller\Adminhtml\System\Currencysymbol\Save
     */
    protected $action;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\CurrencySymbol\Model\System\Currencysymbol|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $currencySymbolMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirectMock;

    /**
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \Magento\Framework\Filter\FilterManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterManagerMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->getMock(\Magento\Framework\App\RequestInterface::class, [], [], '', false);

        $this->helperMock = $this->getMock(\Magento\Backend\Helper\Data::class, [], [], '', false);

        $this->redirectMock = $this->getMock(
            \Magento\Framework\App\Response\RedirectInterface::class,
            [],
            [],
            '',
            false
        );

        $this->responseMock = $this->getMock(
            \Magento\Framework\App\ResponseInterface::class,
            ['setRedirect', 'sendResponse'],
            [],
            '',
            false
        );

        $this->currencySymbolMock = $this->getMock(
            \Magento\CurrencySymbol\Model\System\Currencysymbol::class,
            [],
            [],
            '',
            false
        );

        $this->filterManagerMock = $this->getMock(
            \Magento\Framework\Filter\FilterManager::class,
            ['stripTags'],
            [],
            '',
            false
        );

        $this->objectManagerMock = $this->getMock(\Magento\Framework\ObjectManagerInterface::class, [], [], '', false);

        $this->messageManagerMock = $this->getMock(
            \Magento\Framework\Message\ManagerInterface::class,
            [],
            [],
            '',
            false
        );
        $this->action = $objectManager->getObject(
            \Magento\CurrencySymbol\Controller\Adminhtml\System\Currencysymbol\Save::class,
            [
                'request' => $this->requestMock,
                'response' => $this->responseMock,
                'objectManager' => $this->objectManagerMock,
                'redirect' => $this->redirectMock,
                'helper' => $this->helperMock,
                'messageManager' => $this->messageManagerMock
            ]
        );
    }

    public function testExecute()
    {
        $firstElement = 'firstElement';
        $symbolsDataArray = [$firstElement];
        $redirectUrl = 'redirectUrl';

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('custom_currency_symbol')
            ->willReturn($symbolsDataArray);

        $this->helperMock->expects($this->once())->method('getUrl')->with('*');
        $this->redirectMock->expects($this->once())->method('getRedirectUrl')->willReturn($redirectUrl);

        $this->currencySymbolMock->expects($this->once())->method('setCurrencySymbolsData')->with($symbolsDataArray);
        $this->responseMock->expects($this->once())->method('setRedirect');

        $this->filterManagerMock->expects($this->once())
            ->method('stripTags')
            ->with($firstElement)
            ->willReturn($firstElement);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(\Magento\CurrencySymbol\Model\System\Currencysymbol::class)
            ->willReturn($this->currencySymbolMock);

        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with(\Magento\Framework\Filter\FilterManager::class)
            ->willReturn($this->filterManagerMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccess')
            ->with(__('You applied the custom currency symbols.'));

        $this->action->execute();
    }
}
