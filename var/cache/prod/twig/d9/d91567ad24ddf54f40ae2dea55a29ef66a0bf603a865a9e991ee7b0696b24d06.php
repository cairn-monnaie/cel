<?php

/* CairnUserCyclosBundle:Config/AccountType:confirm_open.html.twig */
class __TwigTemplate_5ecbad49a7b545dd37ebc6be2613de44dd4e1c55d698cb7ef7ea3f10298abba1 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/AccountType:confirm_open.html.twig", 3);
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
  <h2>";
        // line 8
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "name", array()), "html", null, true);
        echo "</h2>

  <div class=\"well\">
            Attention, ce compte sera accessible par tous les professionnels. 
             <div id=\"confirm_action\"> <em>Confirmer l'ouverture du compte </em>

                 ";
        // line 14
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_start');
        echo "
                 ";
        // line 15
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'rest');
        echo "                                              
                 ";
        // line 16
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_end');
        echo "                                                
            </div> 
    </div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/AccountType:confirm_open.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  57 => 16,  53 => 15,  49 => 14,  40 => 8,  35 => 7,  32 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/AccountType:confirm_open.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/AccountType/confirm_open.html.twig");
    }
}
