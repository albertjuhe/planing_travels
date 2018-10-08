<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 08/10/2018
 * Time: 08:02
 */

namespace App\Application\Command;


/**
 * Class CommandBus
 * @package App\Application\Command
 */
class CommandBus
{

    /** @var array  */
    private $handlers = [];

    /**
     * CommandBus constructor.
     * @param array $handlers
     */
    public function __construct()
    {
        $this->handlers = [];
    }

    public function addHandler($commandName, $commandHandler) {
        $this->handlers[$commandName] = $commandHandler;

    }

    public function handle($command) {
        $commandHandler = $this->handlers[get_class($command)];
        if ($commandHandler === null) {
            throw \Exception();
        }
        $commandHandler->execute($command); //handle or execute

    }

}