<?php

namespace App\Models\Support;

use App\Exceptions\LockException;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use ReflectionException;
use ReflectionFunction;

trait Lock
{
    /**
     * Get cache lock name
     */
    protected function getLockName(): string
    {
        return 'lock.' . $this->getTable() . '.' . $this->getKey();
    }

    /**
     * Get lock exception message
     */
    protected function getLockExceptionMessage(): string
    {
        return trans('miscellaneous.model_in_use', ['name' => $this->getTable() . ':' . $this->getKey()]);
    }

    /**
     * Attempts to acquire lock
     */
    protected function __acquireLock(callable $callback): mixed
    {
        try {
            $reflection = new ReflectionFunction($callback);

            $cache = app()->isProduction() ? Cache::store('redis') : Cache::store();

            return $cache->lock($this->getLockName())->get(function () use ($reflection) {
                return tap(match ($reflection->getNumberOfParameters()) {
                    1 => $reflection->invoke($this->fresh()),
                    0 => $reflection->invoke(),
                    default => throw new InvalidArgumentException(),
                });
            });
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Acquire lock
     */
    public function acquireLock(callable $callback): mixed
    {
        return untap($this->__acquireLock($callback));
    }

    /**
     * Acquire Lock or throw exception
     *
     *
     * @throws LockException
     */
    public function acquireLockOrThrow(callable $callback): mixed
    {
        return with($this->__acquireLock($callback), function ($result) {
            if ($result === false) {
                throw new LockException($this->getLockExceptionMessage());
            }

            return untap($result);
        });
    }

    /**
     * Acquire Lock or throw Http Exception
     */
    public function acquireLockOrAbort(callable $callback): mixed
    {
        try {
            return $this->acquireLockOrThrow($callback);
        } catch (LockException $exception) {
            abort(403, $exception->getMessage());
        }
    }
}
