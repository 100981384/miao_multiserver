<?php
    require HELPER_PATH.'/SourceQuery/bootstrap.php';
    use xPaw\SourceQuery\SourceQuery;
	class MinecraftRcon {
	    private $Query;
        public function __construct($port,$password)
        {
            define('SQ_SERVER_ADDR', '127.0.0.1');
            define('SQ_SERVER_PORT', $port);
            define('SQ_TIMEOUT', 1);
            define('SQ_ENGINE', SourceQuery::SOURCE);
            $this->Query = new SourceQuery();
            $this->Query->Connect(SQ_SERVER_ADDR, SQ_SERVER_PORT, SQ_TIMEOUT, SQ_ENGINE);
            $this->Query->SetRconPassword($password);
        }
        public function sendCommand($command){
            try
            {
                 return $this->Query->Rcon( $command );
            }
            catch( Exception $e )
            {
//               echo $e->getMessage( );
            }
            finally
            {
//                $this->Query->Disconnect( );
            }
        }
        function __destruct() {
            $this->Query->Disconnect( );
        }
	}
	













