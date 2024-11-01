<?php

namespace App\CoinAdapters\Contracts;

use App\CoinAdapters\Resources\Address;
use App\CoinAdapters\Resources\Transaction;
use App\CoinAdapters\Resources\Wallet;

interface Adapter
{
    /**
     * Get adapter name
     */
    public function getAdapterName(): string;

    /**
     * Get coin name
     */
    public function getName(): string;

    /**
     * Get coin identifier
     */
    public function getIdentifier(): string;

    /**
     * Get coin unit
     */
    public function getBaseUnit(): string;

    /**
     * Get coin precision
     */
    public function getPrecision(): int;

    /**
     * Get currency precision
     */
    public function getCurrencyPrecision(): int;

    /**
     * Get coin symbol
     */
    public function getSymbol(): string;

    /**
     * Show symbol first
     */
    public function showSymbolFirst(): bool;

    /**
     * Get color used for highlighting
     */
    public function getColor(): string;

    /**
     * Get svg icon url
     */
    public function getSvgIcon(): string;

    /**
     * Generate wallet
     */
    public function createWallet(string $passphrase): Wallet;

    /**
     * Create address for users
     */
    public function createAddress(Wallet $wallet, string $passphrase, string $label = null): Address;

    /**
     * Send transaction
     */
    public function send(Wallet $wallet, string $address, string $amount, string $passphrase): Transaction;

    /**
     * Get wallet transaction by id
     */
    public function getTransaction(Wallet $wallet, string $id): Transaction;

    /**
     * Handle coin webhook and return the transaction data
     */
    public function handleTransactionWebhook(Wallet $wallet, array $payload): ?Transaction;

    /**
     * Add webhook for wallet.
     */
    public function setTransactionWebhook(Wallet $wallet, int $minConf = 3): void;

    /**
     * Reset webhook for wallet.
     */
    public function resetTransactionWebhook(Wallet $wallet, int $minConf = 3): void;

    /**
     * Get the dollar price
     */
    public function getDollarPrice(): float;

    /**
     * Get last 24hr change
     */
    public function getDollarPriceChange(): float;

    /**
     * Get market chart
     */
    public function getMarketChart(string $interval): array;

    /**
     * Estimate the transaction fee
     */
    public function estimateTransactionFee(string $amount, int $inputs): string;

    /**
     * Get minimum transferable amount.
     */
    public function getMinimumTransferable(): string;

    /**
     * Get maximum transferable amount.
     */
    public function getMaximumTransferable(): string;
}
