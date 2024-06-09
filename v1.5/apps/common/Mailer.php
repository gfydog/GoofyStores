<?php
/**
 * The Mailer class allows sending emails with specified parameters.
 */
class Mailer {
    private $from;      // The sender's email address
    private $to;        // The recipient's email address
    private $subject;   // The email subject
    private $message;   // The email content
    private $headers;   // Additional email headers

    /**
     * Constructor to initialize the sender's email address and set up email headers.
     *
     * @param string $from The sender's email address.
     *
     * @throws InvalidArgumentException if the provided sender's email is not valid.
     */
    public function __construct($from) {
        if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("The sender's email address is not valid.");
        }

        $this->from = $from;
        $this->headers = "MIME-Version: 1.0" . "\r\n";
        $this->headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $this->headers .= "From: Goofy <" . $this->from . ">\r\n";
    }

    /**
     * Set the recipient's email address.
     *
     * @param string $to The recipient's email address.
     *
     * @throws InvalidArgumentException if the provided recipient's email is not valid.
     */
    public function setRecipient($to) {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("The recipient's email address is not valid.");
        }
        
        $this->to = $to;
    }

    /**
     * Set the email subject.
     *
     * @param string $subject The email subject.
     */
    public function setSubject($subject) {
        $this->subject = $subject;
    }

    /**
     * Set the email content.
     *
     * @param string $message The email content.
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * Send the email with the configured parameters.
     *
     * @return bool True if the email was sent successfully, false otherwise.
     */
    public function sendEmail() {
        if (!$this->to || !$this->subject || !$this->message) {
            return false;
        }

        if (!mail($this->to, $this->subject, $this->message, $this->headers)) {
            return false;
        }

        return true;
    }
}
