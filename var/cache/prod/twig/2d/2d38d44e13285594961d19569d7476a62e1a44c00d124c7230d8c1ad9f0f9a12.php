<?php

/* CairnUserCyclosBundle:Config/Group/Network:edit.html.twig */
class __TwigTemplate_0e4ac4e14c9885f2332c88ba346c821f32269fec357f0d1a364ea473ea840386 extends Twig_Template
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
        return "CairnUserCyclosBundle:Config/Group/Network:edit.html.twig";
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
        return new Twig_Source("", "CairnUserCyclosBundle:Config/Group/Network:edit.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/Group/Network/edit.html.twig");
    }
}
