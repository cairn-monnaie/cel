<?php

/* CairnUserBundle:Banking:reconversion.html.twig */
class __TwigTemplate_60427c68c33de426da7bb121241b81ba716970b6ac9ccc5b31a752377cf84a0b extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:reconversion.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_body($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "

    <div class=\"well\">
      ";
        // line 9
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_start');
        echo "
      ";
        // line 10
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(twig_get_attribute($this->env, $this->source, ($context["form"] ?? null), "fromAccount", array()), 'widget', array("attr" => array("class" => "hidden-row")));
        echo "
      ";
        // line 11
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'rest');
        echo "
      ";
        // line 12
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_end');
        echo "

    </div>

    ";
        // line 16
        echo twig_include($this->env, $context, "CairnUserBundle:Banking:accounts_list.html.twig", array("accounts" => ($context["accounts"] ?? null), "type" => "debit"));
        echo "

";
    }

    // line 20
    public function block_javascripts($context, array $blocks = array())
    {
        // line 21
        echo "    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>

    <script>
        jQuery(function (\$) {
            \$formAccountOwner = \$('#reconversion_fromAccount_owner');    
            \$formAccountId = \$('#reconversion_fromAccount_id');    
           
            \$containerAccount = \$('.account'); 
            \$containerAccount.click(function (e){
                \$formAccountOwner.val(\$(this).children(\"em\")[0].innerText) ;
                \$formAccountId.val(\$(this).children(\"span:last\")[0].innerText);
            });
        });
    </script>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:reconversion.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  72 => 21,  69 => 20,  62 => 16,  55 => 12,  51 => 11,  47 => 10,  43 => 9,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:reconversion.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/reconversion.html.twig");
    }
}
