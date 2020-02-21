<?php

class MultiThreading {
    
    protected $function;

    protected $data;

    protected $process_ids;

    public function __construct(Collection $data, Callable $function) {

        $this->function = $function;

        $this->data = $data;
    }

    public function run() {

        foreach ($this->data as $item) {

            $pid = pcntl_fork();

            if ($pid == -1) {

                throw new Exception(get_class() . ' : Error forking');

            } else if ($pid == 0) {

                call_user_func_array($this->function, [$item]);

                exit;

            } else {

                $this->process_ids[] = $pid;
            }
        }

        while (!empty($this->process_ids)) {

            foreach ($this->process_ids as $key => $pid) {

                $res = pcntl_waitpid($pid, $status);
                
                if ($res != -1 || $res > 0) {

                    // echo "Process {$res} exited";

                    unset($this->process_ids[$key]);
                }

                pcntl_signal_dispatch();
            }
        }
    }
}