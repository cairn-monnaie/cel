<?php

/* CairnUserBundle:Emails:email_confirmation.html.twig */
class __TwigTemplate_beee0a3bee651f6a35a2e318cc2d54f12699c3980eed6571855a9c5118119fe8 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
            'subject' => array($this, 'block_subject'),
            'body_text' => array($this, 'block_body_text'),
            'body_html' => array($this, 'block_body_html'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        echo "
";
        // line 3
        $this->displayBlock('subject', $context, $blocks);
        // line 4
        echo "
";
        // line 5
        $this->displayBlock('body_text', $context, $blocks);
        // line 21
        echo "
";
        // line 22
        $this->displayBlock('body_html', $context, $blocks);
    }

    // line 3
    public function block_subject($context, array $blocks = array())
    {
        echo " Mon compte Cairn - Validation email ";
    }

    // line 5
    public function block_body_text($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        // line 7
        echo "        Bonjour ";
        echo twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "username", array());
        echo " !

        Vous avez soumis une demande d'inscription via cette adresse mail. Il vous reste ";
        // line 9
        echo ($context["cairn_email_activation_delay"] ?? null);
        echo " jours pour la confirmer.
        Vous pouvez valider votre adresse mail en cliquant sur ce lien : ";
        // line 10
        echo ($context["confirmationUrl"] ?? null);
        echo "

        Une validation de l'équipe administrative sera ensuite nécessaire pour valider définitivement votre compte.

        Le Cairn,


        NB : cet email contient des informations sensibles. Notez-les et supprimez le mail

    ";
    }

    // line 22
    public function block_body_html($context, array $blocks = array())
    {
        // line 23
        echo "    Pseudo : ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "username", array()), "html", null, true);
        echo "
    Mot de passe  ";
        // line 24
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "plainPassword", array()), "html", null, true);
        echo "

";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:email_confirmation.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  86 => 24,  81 => 23,  78 => 22,  64 => 10,  60 => 9,  54 => 7,  52 => 6,  49 => 5,  43 => 3,  39 => 22,  36 => 21,  34 => 5,  31 => 4,  29 => 3,  26 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:email_confirmation.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/email_confirmation.html.twig");
    }
}
