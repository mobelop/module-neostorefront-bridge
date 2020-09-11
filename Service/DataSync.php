<?php

namespace Mobelop\Bridge\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class DataSync
{
    const CONFIG_PATH_BRIDGE_ENABLED = 'mobelop/bridge/enabled';
    const CONFIG_PATH_BRIDGE_API_KEY = 'mobelop/bridge/api_key';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * DataSync constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $action
     * @param string $entityType
     * @param $entityId
     * @param int $storeId
     * @return void
     */
    public function postUpdate(string $action, string $entityType, $entityId, $storeId = 0): void
    {
        if(!$this->isEnabled()) {
            return;
        }

        $storeUrl = $this->storeManager->getStore($storeId)->getBaseUrl();
        $result = $this->makeGraphqlRequest($storeUrl, $action . ':' . $entityType, $entityId);
        // TODO: log errors
    }

    /**
     * @param string $storeUrl
     * @param string $type
     * @param string $value
     * @return bool|string
     */
    private function makeGraphqlRequest(string $storeUrl, string $type, string $value)
    {
        $url = 'https://pubsub.mobelop.com/graphql';

        $curl = curl_init();

        $data = [];

        $query = 'mutation PushUpdate($storeUrl: String!, $type: String!, $value: String!) {
  pushUpdate(input: {
    storeUrl: $storeUrl
    type: $type
    value: $value
  })
}';

        $data["query"] = $query;
        $data["variables"] = [
            'storeUrl' => $storeUrl,
            'type' => $type,
            'value' => $value
        ];

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'content-type: application/json',
            'x-api-key: ' . $this->getApiKey()
        ]);

        return curl_exec($curl);
    }

    private function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_BRIDGE_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    private function getApiKey()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_BRIDGE_API_KEY, ScopeInterface::SCOPE_STORE);
    }
}
