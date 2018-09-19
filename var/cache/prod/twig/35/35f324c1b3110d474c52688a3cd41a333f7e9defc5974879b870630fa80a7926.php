<?php

/* FOSUserBundle:Group:show_content.html.twig */
class __TwigTemplate_d910b52d5a0e4fd2c0046cb0a997fb44b6e8cf8e7c8b827e47fede06ab22d720 extends Twig_Template
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
        // line 2
        echo "
<div class=\"fos_user_group_show\">
    <p>";
        // line 4
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("group.show.name", array(), "FOSUserBundle"), "html", null, true);
        echo ": ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["group"] ?? null), "getName", array(), "method"), "html", null, true);
        echo "</p>
</div>
";
    }

    public function getTemplateName()
    {
        return "FOSUserBundle:Group:show_content.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  27 => 4,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "FOSUserBundle:Group:show_content.html.twig", "/var/www/Symfony/CairnB2B/vendor/friendsofsymfony/user-bundle/Resources/views/Group/show_content.html.twig");
    }
}
