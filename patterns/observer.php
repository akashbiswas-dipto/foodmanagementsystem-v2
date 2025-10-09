<?php
declare(strict_types=1);

namespace AppObservers;

interface ObserverInterface {
    public function update(string $message): void;
}

class NotifierSubject {
    private array $observers = [];

    public function attach(ObserverInterface $obs): void {
        $this->observers[] = $obs;
    }

    public function detach(ObserverInterface $obs): void {
        foreach ($this->observers as $i => $o) {
            if ($o === $obs) unset($this->observers[$i]);
        }
    }

    public function notifyAll(string $msg): void {
        foreach ($this->observers as $obs) {
            $obs->update($msg);
        }
    }
}

class LogObserver implements ObserverInterface {
    public function update(string $message): void {
        error_log("[Notifier] " . $message);
    }
}
