<?php
/**
 * A mail message compiled using a twig environment.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBMailMessageTwig extends CBMailMessage
{
	/**
	 * Twig environment which does the rendering.
	 * @var Twig_Environment
	 */
	public $twigEnvironment;
	/**
	 * Template for message body.
	 * @var string
	 */
	public $bodyTemplate;
	/**
	 * Template for message subject.
	 * @var string
	 */
	public $subjectTemplate;
	/**
	 * Template variables.
	 * @var array
	 */
	public $templateVars;

	public function compileSubject()
	{
		$this->subject = $this->twigEnvironment->render($this->subjectTemplate, $this->templateVars);
	}

	public function compileBody()
	{
		$this->body = $this->twigEnvironment->render($this->bodyTemplate, $this->templateVars);
	}

	public function compile()
	{
		$this->compileSubject();
		$this->compileBody();
	}
}
