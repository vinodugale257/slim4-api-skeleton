<?php
namespace Library;

class Encryption
{
    protected $m_strEncryptionMethod = 'AES-128-CTR';
    private $m_strKey;

    public function __construct($strKey = 'TEST', $strMethod = null)
    {
        if (!valStr($strKey)) {
            die(__METHOD__ . ': Invalid key: ' . $strKey);
        }

        if (ctype_print($strKey)) {
            // convert key to binary format
            $this->m_strKey = openssl_digest($strKey, 'SHA256', true);
        } else {
            $this->m_strKey = $strKey;
        }

        if ($strMethod) {
            if (in_array($strMethod, openssl_get_cipher_methods())) {
                $this->m_strEncryptionMethod = $strMethod;
            } else {
                die(__METHOD__ . ': unrecognised encryption method: ' . $strMethod);
            }
        }
    }

    public function encryptText($strText)
    {
        $strIv              = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->m_strEncryptionMethod));
        $strEncryptedText   = bin2hex($strIv) . openssl_encrypt($strText, $this->m_strEncryptionMethod, $this->m_strKey, 0, $strIv);
        return $strEncryptedText;
    }

    public function decryptText($strEncryptedData)
    {
        $intIvLength = 2 * ( openssl_cipher_iv_length($this->m_strEncryptionMethod) );

        if (preg_match('/^(.{' . $intIvLength . '})(.+)$/', $strEncryptedData, $arrstrRegs)) {
            list( , $strIv, $strEncryptedText ) = $arrstrRegs;
            $strDecryptedText = openssl_decrypt($strEncryptedText, $this->m_strEncryptionMethod, $this->m_strKey, 0, hex2bin($strIv));
            return $strDecryptedText;
        } else {
            return false;
        }
    }
}
