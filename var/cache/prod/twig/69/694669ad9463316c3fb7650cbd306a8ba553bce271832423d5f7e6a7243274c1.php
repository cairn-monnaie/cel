<?php

/* CairnUserBundle:Banking:conversion.html.twig */
class __TwigTemplate_5a5c6a07b60dc1c6ba6725c2eb04fbd020e77024b6e8d72a43311eee765aa2ff extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:conversion.html.twig", 3);
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

    ";
        // line 8
        if ((($context["to"] ?? null) == "other")) {
            // line 9
            echo "    <div class=\"well\">
      ";
            // line 10
            echo             $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["formUser"] ?? null), 'form_start');
            echo "
      ";
            // line 11
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["formUser"] ?? null), 'rest');
            echo "
      ";
            // line 12
            echo             $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["formUser"] ?? null), 'form_end');
            echo "

    </div>
    ";
        }
        // line 16
        echo "
    <div class=\"well\">
      ";
        // line 18
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["formConversion"] ?? null), 'form_start');
        echo "
      ";
        // line 19
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(twig_get_attribute($this->env, $this->source, ($context["formConversion"] ?? null), "toAccount", array()), 'widget', array("attr" => array("class" => "hidden-row")));
        echo "
      ";
        // line 20
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["formConversion"] ?? null), 'rest');
        echo "
      ";
        // line 21
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["formConversion"] ?? null), 'form_end');
        echo "

    </div>

    ";
        // line 25
        echo twig_include($this->env, $context, "CairnUserBundle:Banking:accounts_list.html.twig", array("accounts" => ($context["accounts"] ?? null), "type" => "credit"));
        echo "

";
    }

    // line 29
    public function block_javascripts($context, array $blocks = array())
    {
        // line 30
        echo "    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>

    <script>
        jQuery(function (\$) {
            \$formAccountOwner = \$('#conversion_toAccount_owner');    
            \$formAccountId = \$('#conversion_toAccount_id');    
           
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
        return "CairnUserBundle:Banking:conversion.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  95 => 30,  92 => 29,  85 => 25,  78 => 21,  74 => 20,  70 => 19,  66 => 18,  62 => 16,  55 => 12,  51 => 11,  47 => 10,  44 => 9,  42 => 8,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:conversion.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/conversion.html.twig");
    }
}
