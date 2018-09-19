<?php

/* CairnUserBundle:BankConnection:new_envelope.html.twig */
class __TwigTemplate_927dce111980e66f4137a4453481591d7e968f4398af3ca2107b702fb2725a45 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserBundle:BankConnection:new_envelope.html.twig", 3);
        $this->blocks = array(
            'fos_user_content' => array($this, 'block_fos_user_content'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserCyclosBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_fos_user_content($context, array $blocks = array())
    {
        // line 6
        echo "
<h3>Formulaire d'enrigstrement d'une nouvelle enveloppe</h3>

<div class=\"well\">
  ";
        // line 10
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form');
        echo "
 ";
        // line 11
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(twig_get_attribute($this->env, $this->source, ($context["form"] ?? null), "banknotes", array()), 'row');
        echo "
  <a href=\"#\" id=\"add_banknote\" class=\"btn btn-default\">Ajouter un billet</a>
</div>
";
    }

    // line 16
    public function block_javascripts($context, array $blocks = array())
    {
        // line 17
        echo "
    <script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>
    <script>
     </script>

//    <script type=\"text/javascript\" src=\"withdrawal.js\"></script>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:BankConnection:new_envelope.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  57 => 17,  54 => 16,  46 => 11,  42 => 10,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:BankConnection:new_envelope.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/BankConnection/new_envelope.html.twig");
    }
}
