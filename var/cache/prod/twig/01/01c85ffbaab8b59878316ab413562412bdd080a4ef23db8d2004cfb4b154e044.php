<?php

/* CairnUserCyclosBundle:Config/AccountType:add.html.twig */
class __TwigTemplate_a2a61946b8fefc03b57dc47c12a4bb575b0d2c65a5a4b73e44fff2844f249b95 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/AccountType:add.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
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

    // line 6
    public function block_body($context, array $blocks = array())
    {
        // line 7
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "
    <h3>Formulaire d'ajout</h3></br>
    <em>Attention, le nom du compte est définitif. Il ne peut être ensuite modifié </em>    
    <div class=\"well\">
      ";
        // line 11
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form');
        echo "
    </div>

";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/AccountType:add.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  43 => 11,  35 => 7,  32 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/AccountType:add.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/AccountType/add.html.twig");
    }
}
