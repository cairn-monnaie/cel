<?php

/* @CairnUser/Pro/crash.js */
class __TwigTemplate_eb94232751b3c1f2c5a7b2a2899f9d8e4b9766cd2c35ce17191f1d74e3894568 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "jQuery(function(\$){
    \$('#consulting').mouseover(function(e) {
       \$(this).append(<a href=\"";
        // line 3
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_management_beneficiaries"), "js", null, true);
        echo "\" class=\"btn btn-default\">
) 
    });
});
";
    }

    public function getTemplateName()
    {
        return "@CairnUser/Pro/crash.js";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  27 => 3,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@CairnUser/Pro/crash.js", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pro/crash.js");
    }
}
