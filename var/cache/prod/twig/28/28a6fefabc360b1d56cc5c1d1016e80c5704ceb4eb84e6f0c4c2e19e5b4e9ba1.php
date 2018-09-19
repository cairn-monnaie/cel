<?php

/* CairnUserCyclosBundle:Config/Group:remove.html.twig */
class __TwigTemplate_e0d01dfc01bae3b636278ae80eb2a76784c723c2563f403826e6f47a4ed99590 extends Twig_Template
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
<h3>Formulaire d'annonce</h3>

<div class=\"well\">
  ";
        // line 6
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form');
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/Group:remove.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  29 => 6,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/Group:remove.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/Group/remove.html.twig");
    }
}
