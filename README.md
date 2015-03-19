# Poirot\Stream

## Connection Oriented Transports (such as TCP)

__Server__

```php
ob_end_clean();
header("Connection: keep-alive");
ignore_user_abort(); // optional

ob_start();
echo ('Server Listening ....');
ob_end_flush(); // Strange behaviour, will not work
flush();

$connect = function()
{
    // Create TCP Server And Bind On Port #8000
    $socket = new StreamServer('tcp://0.0.0.0:8000');
    $socket->bind();

    return $socket;
};

/** @var StreamServer $socketServer */
$socketServer = $connect();
while (1)
{
    try {
        // Listen On Port Till Client Connect ...
        $streamable = $socketServer->listen();
    } catch (TimeoutException $e) {
        // If Connection Timeout Connect Again, and listen
        $socketServer->shutdown();     // free server port
        $socketServer = $connect();    // connect again ..
        continue;
    }

    ob_start();
    echo '<br/><br/>'."Client Connected: "
        .(
            $streamable->getResource()
                ->getRemoteName()
        );
    ob_end_flush();flush();

    // While Client Not Closed Connection ...
    $response = '';
    while ($streamable->getResource()->isAlive())
    {
        $clientMessage = $streamable->readLine();

        ob_start();
        switch ($clientMessage) {
            case 'bye':
                echo '<br/>'."> bye "
                    .(
                    $streamable->getResource()
                        ->getRemoteName()
                    );

                // send back response
                $streamable->write($response);

                // close client connection
                $streamable->getResource()->close();
                break;
            case 'time':
                echo '<br/> >'.$clientMessage;
                $response .= date("D M j H:i:s Y\r\n");
                break;
            default:
                echo '<br/>'.'>'.$clientMessage. ', Not Recognized.';
        }
        ob_end_flush();flush();
    }
}
```

__Client__

```php
$socks  = new StreamClient('tcp://127.0.0.1:8000');

try {
    $resrc = $socks->getConnect();
} catch (\Exception $e) {
    throw new \Exception('Not Connected.', null, $e);
}

$clientStream = new Streamable($resrc);
echo $clientStream->getResource()->getLocalName();

$clientStream->sendData("time\n");
$clientStream->sendData("not known command\n");
$clientStream->sendData("bye\n");

// Get Back Response
echo $clientStream->read();

```
