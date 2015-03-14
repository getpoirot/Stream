<?php
namespace Poirot\Stream\Context\Http;

use Poirot\Core\AbstractOptions;

class SCHttpOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $peerName;

    /**
     * @var boolean
     */
    protected $verifyPeer;

    /**
     * @var boolean
     */
    protected $verifyPeerName;

    /**
     * @var boolean
     */
    protected $allowSelfSigned;

    /**
     * @var string
     */
    protected $cafile;

    /**
     * @var string
     */
    protected $capath;

    /**
     * @var string
     */
    protected $localCert;

    /**
     * @var string
     */
    protected $passphrase;

    /**
     * @var integer
     */
    protected $verifyDepth;

    /**
     * @var string
     */
    protected $ciphers;

    /**
     * @var boolean
     */
    protected $capturePeerCert;

    /**
     * @var boolean
     */
    protected $capturePeerCertChain;

    /**
     * @var string
     */
    protected $SniEnable;

    /**
     * @var string
     */
    protected $sniServerName;

    /**
     * @var boolean
     */
    protected $disableCompression;

    /**
     * @var string|array
     */
    protected $peerFingerprint;

    /**
     * @return array|string
     */
    public function getPeerFingerprint()
    {
        return $this->peerFingerprint;
    }

    /**
     * @param array|string $peerFingerprint
     */
    public function setPeerFingerprint($peerFingerprint)
    {
        $this->peerFingerprint = $peerFingerprint;
    }

    /**
     * @return mixed
     */
    public function getDisableCompression()
    {
        return $this->disableCompression;
    }

    /**
     * @param mixed $disableCompression
     */
    public function setDisableCompression($disableCompression)
    {
        $this->disableCompression = $disableCompression;
    }

    /**
     * @return string
     */
    public function getSniServerName()
    {
        return $this->sniServerName;
    }

    /**
     * @param string $sniServerName
     */
    public function setSniServerName($sniServerName)
    {
        $this->sniServerName = $sniServerName;
    }

    /**
     * @return string
     */
    public function getSniEnable()
    {
        return $this->SniEnable;
    }

    /**
     * @param string $SniEnable
     */
    public function setSniEnable($SniEnable)
    {
        $this->SniEnable = $SniEnable;
    }

    /**
     * @return mixed
     */
    public function getCapturePeerCertChain()
    {
        return $this->capturePeerCertChain;
    }

    /**
     * @param mixed $capturePeerCertChain
     */
    public function setCapturePeerCertChain($capturePeerCertChain)
    {
        $this->capturePeerCertChain = $capturePeerCertChain;
    }

    /**
     * @return mixed
     */
    public function getCapturePeerCert()
    {
        return $this->capturePeerCert;
    }

    /**
     * @param mixed $capturePeerCert
     */
    public function setCapturePeerCert($capturePeerCert)
    {
        $this->capturePeerCert = $capturePeerCert;
    }

    /**
     * @return string
     */
    public function getCiphers()
    {
        return $this->ciphers;
    }

    /**
     * @param string $ciphers
     */
    public function setCiphers($ciphers)
    {
        $this->ciphers = $ciphers;
    }

    /**
     * @return int
     */
    public function getVerifyDepth()
    {
        return $this->verifyDepth;
    }

    /**
     * @param int $verifyDepth
     */
    public function setVerifyDepth($verifyDepth)
    {
        $this->verifyDepth = $verifyDepth;
    }

    /**
     * @return string
     */
    public function getPassphrase()
    {
        return $this->passphrase;
    }

    /**
     * @param string $passphrase
     */
    public function setPassphrase($passphrase)
    {
        $this->passphrase = $passphrase;
    }

    /**
     * @return string
     */
    public function getLocalCert()
    {
        return $this->localCert;
    }

    /**
     * @param string $localCert
     */
    public function setLocalCert($localCert)
    {
        $this->localCert = $localCert;
    }

    /**
     * @return string
     */
    public function getCapath()
    {
        return $this->capath;
    }

    /**
     * @param string $capath
     */
    public function setCapath($capath)
    {
        $this->capath = $capath;
    }

    /**
     * @return string
     */
    public function getCafile()
    {
        return $this->cafile;
    }

    /**
     * @param string $cafile
     */
    public function setCafile($cafile)
    {
        $this->cafile = $cafile;
    }

    /**
     * @return mixed
     */
    public function getAllowSelfSigned()
    {
        return $this->allowSelfSigned;
    }

    /**
     * @param mixed $allowSelfSigned
     */
    public function setAllowSelfSigned($allowSelfSigned)
    {
        $this->allowSelfSigned = $allowSelfSigned;
    }

    /**
     * @return mixed
     */
    public function getVerifyPeerName()
    {
        return $this->verifyPeerName;
    }

    /**
     * @param mixed $verifyPeerName
     */
    public function setVerifyPeerName($verifyPeerName)
    {
        $this->verifyPeerName = $verifyPeerName;
    }

    /**
     * @return mixed
     */
    public function getVerifyPeer()
    {
        return $this->verifyPeer;
    }

    /**
     * @param mixed $verifyPeer
     */
    public function setVerifyPeer($verifyPeer)
    {
        $this->verifyPeer = $verifyPeer;
    }

    /**
     * @return string
     */
    public function getPeerName()
    {
        return $this->peerName;
    }

    /**
     * @param string $peerName
     */
    public function setPeerName($peerName)
    {
        $this->peerName = $peerName;
    }
}
 