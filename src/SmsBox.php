<?php

declare(strict_types=1);

namespace JaanBV\SmsBox;

use DateTime;
use JaanBV\SmsBox\Exception\ActionNotProvidedException;
use JaanBV\SmsBox\Exception\ApiKeyNotAllowedException;
use JaanBV\SmsBox\Exception\ApikeyNotProvidedException;
use JaanBV\SmsBox\Exception\ApikeyNotValidException;
use JaanBV\SmsBox\Exception\CreditsAreLowException;
use JaanBV\SmsBox\Exception\CustomException;
use JaanBV\SmsBox\Exception\InvalidHttpResponseCodeException;
use JaanBV\SmsBox\Exception\InvalidResponseCodeException;
use JaanBV\SmsBox\Exception\InvalidTextToSpeechLanguageException;
use JaanBV\SmsBox\Exception\LookupFailedException;
use JaanBV\SmsBox\Exception\MaximumNumberOfPhoneNumbersReachedException;
use JaanBV\SmsBox\Exception\MessageNotProvidedException;
use JaanBV\SmsBox\Exception\NotEnoughCreditsException;
use JaanBV\SmsBox\Exception\NoValidPhoneNumberException;
use JaanBV\SmsBox\Exception\NumberIsAlreadyVerifiedException;
use JaanBV\SmsBox\Exception\NumberNotProvidedException;
use JaanBV\SmsBox\Exception\NumberVerificationFailedException;
use JaanBV\SmsBox\Exception\OneTimePasswordNotCorrectException;
use JaanBV\SmsBox\Exception\OneTimePasswordNotProvidedException;
use JaanBV\SmsBox\Exception\OnlyOneNumberAllowedException;
use JaanBV\SmsBox\Exception\PhoneNumberHasNoPrefixException;
use JaanBV\SmsBox\Exception\PhoneNumberInvalidException;
use JaanBV\SmsBox\Exception\PhoneNumberNotProvidedException;
use JaanBV\SmsBox\Exception\PhoneNumberOfSenderIsToLongException;
use JaanBV\SmsBox\Exception\SenderNotOkException;
use JaanBV\SmsBox\Exception\SendVoiceFailedException;
use JaanBV\SmsBox\Exception\SmsBoxException;
use JaanBV\SmsBox\Exception\SmsHasNoMessageException;
use JaanBV\SmsBox\Exception\SmsMaxCharactersReachedException;
use JaanBV\SmsBox\Exception\TryAgainLaterException;
use JaanBV\SmsBox\Exception\UnknownStatusException;
use JaanBV\SmsBox\SentSms\Results;
use JaanBV\SmsBox\Voice\VoiceSentResult;
use JaanBV\SmsBox\Voice\VoiceGender;
use JaanBV\SmsBox\Voice\VoiceLanguage;

/**
 * SmsBox
 *
 * This SmsBox PHP Class connects to the SmsBox API.
 *
 * Possible methods
 * - balance
 * - isValidPhoneNumber
 * - retrievePhoneNumberData
 * - sendSms
 * - sendSmsWithOneTimePassword
 * - verifyOneTimePassword
 */
final class SmsBox
{
    private const API_URL = 'https://core.smsbox.be/api/v1';
    private const API_ENDPOINT_FOR_AUTHENTICATE = 'auth';
    private const API_ENDPOINT_FOR_BALANCE = 'balance';
    private const API_ENDPOINT_FOR_ONE_TIME_PASSWORD_SEND = 'otp/send';

    private const API_ENDPOINT_FOR_ONE_TIME_PASSWORD_VERIFY = 'otp/verify';
    private const API_ENDPOINT_FOR_SEND_SMS = 'sendsms';
    private const API_ENDPOINT_FOR_SEND_VOICE = 'voice/send';
    private const API_ENDPOINT_FOR_PHONE_NUMBER_VALIDATION = 'hlr';
    private const KEY_FOR_CODE = 'code';
    private const KEY_FOR_CUSTOM_ONE_TIME_PASSWORD_MESSAGE = 'text';
    private const KEY_FOR_FROM_PHONE_NUMBER = 'from';
    private const KEY_FOR_GENDER = 'gender';
    private const KEY_FOR_LANGUAGE = 'lang';
    private const KEY_FOR_LONG_SMS = 'longsms';
    private const KEY_FOR_ONE_TIME_PASSWORD_CODE = 'otp';
    private const KEY_FOR_NUMBER = 'number';
    private const KEY_FOR_EMAIL = 'email';
    private const KEY_FOR_FROM_EMAIL = "from_email";
    private const KEY_FOR_NUMBERS = 'numbers';
    private const KEY_FOR_NOTIFICATION_WEBHOOK = 'noti';
    private const KEY_FOR_MESSAGE = 'message';
    private const KEY_FOR_SEND_AT = 'send_at';
    private const KEY_FOR_TEXT_TO_SPEECH = 'tts';
    private const KEY_FOR_TEXT_TO_SPEECH_LANGUAGE = 'ttslng';

    private const REQUEST_TYPE_GET = 'GET';
    private const REQUEST_TYPE_POST = 'POST';

    private const RESPONSE_CODE_FOR_VALID_CALL_TO_AUTHENTICATE_API_ENDPOINT = 10;
    private const RESPONSE_CODES_FOR_INVALID_CALL_TO_AUTHENTICATE_API_ENDPOINT = [
        20 => ApikeyNotProvidedException::class,
        21 => ApikeyNotAllowedException::class,
    ];

    private const RESPONSE_CODE_FOR_VALID_CALL_TO_BALANCE_API_ENDPOINT = 100;
    private const RESPONSE_CODES_FOR_INVALID_CALL_TO_BALANCE_API_ENDPOINT = [
        4 => ApikeyNotValidException::class,
    ];

    private const RESPONSE_CODE_FOR_VALID_CALL_TO_SEND_SMS_API_ENDPOINT = 100;
    private const RESPONSE_CODES_FOR_INVALID_CALL_TO_SEND_SMS_API_ENDPOINT = [
        1 => SenderNotOkException::class,
        4 => ApikeyNotValidException::class,
        5 => PhoneNumberHasNoPrefixException::class,
        6 => SmsHasNoMessageException::class,
        7 => SmsMaxCharactersReachedException::class,
        8 => NumberNotProvidedException::class,
        9 => NoValidPhoneNumberException::class,
        10 => NotEnoughCreditsException::class,
        11 => MaximumNumberOfPhoneNumbersReachedException::class,
        13 => PhoneNumberOfSenderIsToLongException::class,
    ];

    private const RESPONSE_CODE_FOR_VALID_CALL_TO_SEND_VOICE_API_ENDPOINT = 10;
    private const RESPONSE_CODES_FOR_INVALID_CALL_TO_SEND_VOICE_API_ENDPOINT = [
        20 => ApikeyNotProvidedException::class,
        21 => ApikeyNotValidException::class,
        30 => PhoneNumberNotProvidedException::class,
        31 => PhoneNumberInvalidException::class,
        40 => MessageNotProvidedException::class,
        50 => SendVoiceFailedException::class,
        60 => CreditsAreLowException::class,
    ];

    private const RESPONSE_CODE_FOR_VALID_CALL_TO_PHONE_VALIDATION_API_ENDPOINT = 10;
    private const RESPONSE_CODES_FOR_INVALID_CALL_TO_PHONE_VALIDATION_API_ENDPOINT = [
        4 => ApikeyNotValidException::class,
        30 => NumberNotProvidedException::class,
        40 => LookupFailedException::class,
    ];

    private const RESPONSE_CODE_FOR_VALID_CALL_TO_SEND_ONE_TIME_PASSWORD_API_ENDPOINT = 10;
    private const RESPONSE_CODE_FOR_VALID_CALL_TO_VERIFY_ONE_TIME_PASSWORD_API_ENDPOINT = 11;
    private const RESPONSE_CODES_FOR_INVALID_CALL_TO_ONE_TIME_PASSWORD_API_ENDPOINT = [
        11 => NumberIsAlreadyVerifiedException::class,
        20 => ApikeyNotProvidedException::class,
        21 => ApikeyNotValidException::class,
        30 => ActionNotProvidedException::class,
        40 => NumberVerificationFailedException::class,
        41 => NumberNotProvidedException::class,
        42 => OnlyOneNumberAllowedException::class,
        50 => OneTimePasswordNotProvidedException::class,
        51 => OneTimePasswordNotCorrectException::class,
        60 => NotEnoughCreditsException::class,
        90 => CustomException::class,
        98 => TryAgainLaterException::class,
        99 => UnknownStatusException::class,
    ];

    public const SANDBOX_PHONE_NUMBER = '3211111111';

    public const TEXT_TO_SPEECH_DEFAULT = self::TEXT_TO_SPEECH_NL;
    public const TEXT_TO_SPEECH_NL = 0;
    public const TEXT_TO_SPEECH_EN = 1;
    public const TEXT_TO_SPEECH_FR = 2;

    public const POSSIBLE_VALUES_FOR_TEXT_TO_SPEECH_LANGUAGE = [
        self::TEXT_TO_SPEECH_NL => 'NL',
        self::TEXT_TO_SPEECH_EN => 'EN',
        self::TEXT_TO_SPEECH_FR => 'FR',
    ];

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var bool
     */
    private $debug = false;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Test validity for API-key.
     *
     * @return bool
     */
    public function ping() : bool
    {
        $response = $this->doCall(
            self::REQUEST_TYPE_GET,
            self::API_ENDPOINT_FOR_AUTHENTICATE
        );

        $this->throwExceptionIfResponseIsNotValid(
            $response,
            self::RESPONSE_CODE_FOR_VALID_CALL_TO_AUTHENTICATE_API_ENDPOINT,
            self::RESPONSE_CODES_FOR_INVALID_CALL_TO_AUTHENTICATE_API_ENDPOINT
        );

        return true;
    }

    /**
     * Get the remaining credit balance for the current account connected to the API-key.
     *
     * @return float
     */
    public function balance() : float
    {
        $response = $this->doCall(
            self::REQUEST_TYPE_GET,
            self::API_ENDPOINT_FOR_BALANCE
        );

        $this->throwExceptionIfResponseIsNotValid(
            $response,
            self::RESPONSE_CODE_FOR_VALID_CALL_TO_BALANCE_API_ENDPOINT,
            self::RESPONSE_CODES_FOR_INVALID_CALL_TO_BALANCE_API_ENDPOINT
        );

        return (float) $response[self::KEY_FOR_MESSAGE];
    }

    /**
     * Is the given phone number valid?
     *
     * By using this method the "Home Location Register" (HLR) will be connected for the given mobile phone number.
     * Then we are 100% sure this phone number valid and connected on the network of the GSM provider.
     *
     *
     * @param string $phoneNumber
     * @return bool
     */
    public function isValidPhoneNumber(string $phoneNumber) : bool
    {
        try {
            $this->retrievePhoneNumberData($phoneNumber);
        } catch (SmsBoxException $exception) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve phone number data
     *
     * By using this method the "Home Location Register" (HLR) will be connected for the given mobile phone number.
     * Then we are 100% sure this phone number valid and connected on the network of the GSM provider.
     * You retrieve all data provided by the HLR.
     *
     *
     * @param string $phoneNumber
     * @return array
     */
    public function retrievePhoneNumberData(string $phoneNumber) : array
    {
        $response = $this->doCall(
            self::REQUEST_TYPE_GET,
            self::API_ENDPOINT_FOR_PHONE_NUMBER_VALIDATION,
            [
                self::KEY_FOR_NUMBER => urlencode($phoneNumber)
            ]
        );

        $this->throwExceptionIfResponseIsNotValid(
            $response,
            self::RESPONSE_CODE_FOR_VALID_CALL_TO_PHONE_VALIDATION_API_ENDPOINT,
            self::RESPONSE_CODES_FOR_INVALID_CALL_TO_PHONE_VALIDATION_API_ENDPOINT
        );

        return $response[self::KEY_FOR_MESSAGE];
    }

    /**
     * Send an sms, or a sms to voicemail
     *
     * @param array       $toPhoneNumbers - f.e. [3211111111, 32497000000, ...]
     * @param string      $message - Max. 160 characters, unless $allowLongerSms = true, then max. 459 characters. Note: Special characters count for two (\r\n, [, \, ], ^, {, |, }, ~, €.)
     * @param bool        $allowLongerSms - 160 characters = 1 sms, if longer, maximum 459 characters
     * @param string|null $fromPhoneNumber - Only possible for non-belgian receivers
     * @param bool        $convertTextToSpeech - Create a voice mail instead of a text message
     * @param string|null $textToSpeechLanguage - Possible values; 0 = NL, 1 = EN, 2 = FR
     * @param string|null $notificationWebhookUrl - Url with your PHP file which will receive a HTTP-POST with sms delivery report
     * @return Results
     * @throws InvalidTextToSpeechLanguageException
     */
    public function sendSms(
        array $toPhoneNumbers,
        string $message,
        bool $allowLongerSms = true,
        string $fromPhoneNumber = null,
        bool $convertTextToSpeech = false,
        string $textToSpeechLanguage = null,
        string $notificationWebhookUrl = null
    ) : Results {
        $data = [
            self::KEY_FOR_NUMBERS => urlencode(implode(',', $toPhoneNumbers)),
            self::KEY_FOR_MESSAGE => urlencode($message),
        ];

        if (true === $allowLongerSms) {
            $data[self::KEY_FOR_LONG_SMS] = 1;
        }

        if (null !== $fromPhoneNumber) {
            $data[self::KEY_FOR_FROM_PHONE_NUMBER] = $fromPhoneNumber;
        }

        if (true === $convertTextToSpeech) {
            $data[self::KEY_FOR_TEXT_TO_SPEECH] = 1;

            if (null === $textToSpeechLanguage) {
                $textToSpeechLanguage = self::TEXT_TO_SPEECH_DEFAULT;
            }

            if (! array_key_exists($textToSpeechLanguage, self::POSSIBLE_VALUES_FOR_TEXT_TO_SPEECH_LANGUAGE)) {
                throw new InvalidTextToSpeechLanguageException(
                    sprintf(
                        'The given text to speech language id "%1$s" is invalid. Possible values are; "%2$s"',
                        $textToSpeechLanguage,
                        implode(',', array_keys(self::POSSIBLE_VALUES_FOR_TEXT_TO_SPEECH_LANGUAGE))
                    )
                );
            }

            $data[self::KEY_FOR_TEXT_TO_SPEECH_LANGUAGE] = $textToSpeechLanguage;
        }

        if (null !== $notificationWebhookUrl) {
            $data[self::KEY_FOR_NOTIFICATION_WEBHOOK] = $notificationWebhookUrl;
        }

        $response = $this->doCall(
            self::REQUEST_TYPE_POST,
            self::API_ENDPOINT_FOR_SEND_SMS,
            $data
        );

        $this->throwExceptionIfResponseIsNotValid(
            $response,
            self::RESPONSE_CODE_FOR_VALID_CALL_TO_SEND_SMS_API_ENDPOINT,
            self::RESPONSE_CODES_FOR_INVALID_CALL_TO_SEND_SMS_API_ENDPOINT
        );

        return new Results($response[self::KEY_FOR_MESSAGE]);
    }

    /**
     * Send an e-mail with a one time password to an e-mail address
     *
     * @param string $email
     * @param string|null $fromEmail
     * @param string|null $customMessage - You must provide a "{OTP}" in the message, otherwise the default text will be used
     * @return bool
     */
    public function sendEmailWithOneTimePassword(
        string $email,
        string $fromEmail = null,
        string $customMessage = null
    ) : bool
    {
        $data = [
            self::KEY_FOR_EMAIL => $email,
        ];

        if (null !== $customMessage) {
            $data[self::KEY_FOR_CUSTOM_ONE_TIME_PASSWORD_MESSAGE] = urlencode($customMessage);
        }

        if (null !== $fromEmail) {
            $data[self::KEY_FOR_FROM_EMAIL] = $fromEmail;
        }
        return $this->sendOneTimePassword($data);
    }

    /**
     * Send an sms with a one time password to a phone number
     *
     * @param string      $phoneNumber
     * @param string|null $customMessage - You must provide a "{OTP}" in the message, otherwise the default text will be used
     * @return bool
     */
    public function sendSmsWithOneTimePassword(
        string $phoneNumber,
        string $customMessage = null
    ) : bool {
        $data = [
            self::KEY_FOR_NUMBER => urlencode($phoneNumber),
        ];

        if (null !== $customMessage) {
            $data[self::KEY_FOR_CUSTOM_ONE_TIME_PASSWORD_MESSAGE] = urlencode($customMessage);
        }
        return $this->sendOneTimePassword($data);
    }

    /**
     * Send a one time password
     *
     * @param array      $data
     * @return bool
     */
    private function sendOneTimePassword(
        array $data
    ) : bool {
        $response = $this->doCall(
            self::REQUEST_TYPE_POST,
            self::API_ENDPOINT_FOR_ONE_TIME_PASSWORD_SEND,
            $data
        );

        $this->throwExceptionIfResponseIsNotValid(
            $response,
            self::RESPONSE_CODE_FOR_VALID_CALL_TO_SEND_ONE_TIME_PASSWORD_API_ENDPOINT,
            self::RESPONSE_CODES_FOR_INVALID_CALL_TO_ONE_TIME_PASSWORD_API_ENDPOINT
        );

        return true;
    }

    /**
     * Verify the received one time password (received from sendOneTimePassword)
     *
     * @param string $phoneNumber
     * @param string $oneTimePassword
     * @return bool
     */
    public function verifyOneTimePassword(
        string $phoneNumber,
        string $oneTimePassword
    ) : bool {
        $response = $this->doCall(
            self::REQUEST_TYPE_GET,
            self::API_ENDPOINT_FOR_ONE_TIME_PASSWORD_VERIFY,
            [
                self::KEY_FOR_NUMBER => urlencode($phoneNumber),
                self::KEY_FOR_ONE_TIME_PASSWORD_CODE => urlencode($oneTimePassword),
            ]
        );

        $this->throwExceptionIfResponseIsNotValid(
            $response,
            self::RESPONSE_CODE_FOR_VALID_CALL_TO_VERIFY_ONE_TIME_PASSWORD_API_ENDPOINT,
            self::RESPONSE_CODES_FOR_INVALID_CALL_TO_ONE_TIME_PASSWORD_API_ENDPOINT
        );

        return true;
    }

    /**
     * @param string             $phoneNumber
     * @param string             $message
     * @param VoiceLanguage|null $language
     * @param VoiceGender|null   $gender
     * @param DateTime|null      $sendAt
     * @return VoiceSentResult
     */
    public function sendVoice(
        string $phoneNumber,
        string $message,
        VoiceLanguage $language = null,
        VoiceGender $gender = null,
        DateTime $sendAt = null
    ): VoiceSentResult {
        $data = [
            self::KEY_FOR_NUMBER => $phoneNumber,
            self::KEY_FOR_MESSAGE => $message,
        ];

        if (null !== $language) {
            $data[self::KEY_FOR_LANGUAGE] = $language->__toString();
        }

        if (null !== $gender) {
            $data[self::KEY_FOR_GENDER] = $gender->__toString();
        }

        if (null !== $sendAt) {
            $data[self::KEY_FOR_SEND_AT] = $sendAt->format('c');
        }

        $response = $this->doCall(
            self::REQUEST_TYPE_POST,
            self::API_ENDPOINT_FOR_SEND_VOICE,
            $data
        );

        $this->throwExceptionIfResponseIsNotValid(
            $response,
            self::RESPONSE_CODE_FOR_VALID_CALL_TO_SEND_VOICE_API_ENDPOINT,
            self::RESPONSE_CODES_FOR_INVALID_CALL_TO_SEND_VOICE_API_ENDPOINT
        );

        return VoiceSentResult::fromArray($response);
    }

    public function enableDebugging()
    {
        $this->debug = true;
    }

    /**
     * @param string $requestType
     * @param string $endPoint
     * @param array  $data
     * @return array
     * @throws InvalidHttpResponseCodeException
     */
    protected function doCall(
        string $requestType,
        string $endPoint,
        array $data = []
    ) : array {
        $url = sprintf(
            '%1$s/%2$s',
            self::API_URL,
            $endPoint
        );

        if (self::REQUEST_TYPE_GET === $requestType) {
            $url .= '?' . http_build_query($data);
        }

        $headers = [];
        $headers[] = 'X-Api-Key: ' . $this->apiKey;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        if (self::REQUEST_TYPE_POST === $requestType) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        $jsonResponse = curl_exec($curl);
        $response = json_decode($jsonResponse, true);
        $httpResponseCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        if (true === $this->debug) {
            echo $url . '<br/>';
            var_dump($response);
            echo '<br/><br/>';
        }

        if (! in_array($httpResponseCode, [200, 201, 401])) {
            throw new InvalidHttpResponseCodeException(
                'Invalid call to SmsBox',
                $httpResponseCode
            );
        }

        curl_close($curl);

        return $response;
    }

    /**
     * @param array $response
     * @param int   $validResponseCode
     * @param array $invalidExceptionClasses
     * @throws InvalidResponseCodeException
     * @throws SmsBoxException
     */
    private function throwExceptionIfResponseIsNotValid(
        array $response,
        int $validResponseCode,
        array $invalidExceptionClasses
    ) {
        if ($validResponseCode === $response[self::KEY_FOR_CODE]) {
            return;
        }

        if (! array_key_exists($response[self::KEY_FOR_CODE], $invalidExceptionClasses)) {
            throw new InvalidResponseCodeException(
                sprintf(
                    'The given response code "%1$s" is currently not supported
                    in "%3$s" with given response message "%2$s".
                    Please contact a developer or create a merge request to add it.',
                    $response[self::KEY_FOR_CODE],
                    serialize($response[self::KEY_FOR_MESSAGE]),
                    SmsBox::class
                ),
                $response[self::KEY_FOR_CODE]
            );
        }

        /** @var SmsBoxException $exceptionClass */
        $exceptionClass = $invalidExceptionClasses[$response[self::KEY_FOR_CODE]];

        throw new $exceptionClass(
            $response[self::KEY_FOR_MESSAGE],
            $response[self::KEY_FOR_CODE]
        );
    }
}
