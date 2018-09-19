<?php

/* CairnUserBundle:Banking:transaction.html.twig */
class __TwigTemplate_925f38b4547f60786f5d82d5b7f2c3a5a7c208c8a1c32dfb2d69acfb37f1cf03 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:transaction.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:transaction.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:transaction.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 5
    public function block_body($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 6
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "

    <div class=\"well\">
      ";
        // line 9
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new Twig_Error_Runtime('Variable "form" does not exist.', 9, $this->source); })()), 'form_start');
        echo "
      ";
        // line 10
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new Twig_Error_Runtime('Variable "form" does not exist.', 10, $this->source); })()), 'rest');
        echo "
      ";
        // line 11
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new Twig_Error_Runtime('Variable "form" does not exist.', 11, $this->source); })()), 'form_end');
        echo "

    </div>

    ";
        // line 15
        echo twig_include($this->env, $context, "CairnUserBundle:Banking:accounts_list.html.twig", array("accounts" => (isset($context["fromAccounts"]) || array_key_exists("fromAccounts", $context) ? $context["fromAccounts"] : (function () { throw new Twig_Error_Runtime('Variable "fromAccounts" does not exist.', 15, $this->source); })()), "type" => "debit"));
        echo "
    ";
        // line 16
        echo twig_include($this->env, $context, "CairnUserBundle:Banking:accounts_list.html.twig", array("accounts" => (isset($context["toAccounts"]) || array_key_exists("toAccounts", $context) ? $context["toAccounts"] : (function () { throw new Twig_Error_Runtime('Variable "toAccounts" does not exist.', 16, $this->source); })()), "type" => "credit"));
        echo "

";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    // line 20
    public function block_javascripts($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        // line 21
        echo "    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>

    <script>
        jQuery(function (\$) {
            \$creditAccountOwner = \$('[id\$=\"toAccount_owner\"]');    
            \$creditAccountId = \$('[id\$=\"toAccount_id\"]');    
           
            \$debitAccountOwner = \$('[id\$=\"fromAccount_owner\"]');    
            \$debitAccountId = \$('[id\$=\"fromAccount_id\"]');    

            \$containerAccount = \$('.account'); 
            \$containerAccount.click(function (e){
                if(\$(this).attr('id') == 'account_credit'){
                    \$creditAccountOwner.val(\$(this).children(\"em\")[0].innerText) ;
                    \$creditAccountId.val(\$(this).children(\"span:last\")[0].innerText);
                }
                else{
                    \$debitAccountOwner.val(\$(this).children(\"em\")[0].innerText) ;
                    \$debitAccountId.val(\$(this).children(\"span:last\")[0].innerText);
                }
            });
        });
    </script>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:transaction.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  102 => 21,  93 => 20,  80 => 16,  76 => 15,  69 => 11,  65 => 10,  61 => 9,  54 => 6,  45 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/transaction.html.twig #}

{% extends \"CairnUserBundle::layout.html.twig\" %}

{% block body %}
    {{parent()}}

    <div class=\"well\">
      {{ form_start(form) }}
      {{ form_rest(form) }}
      {{ form_end(form) }}

    </div>

    {{include(\"CairnUserBundle:Banking:accounts_list.html.twig\",{'accounts':fromAccounts,'type':'debit'})}}
    {{include(\"CairnUserBundle:Banking:accounts_list.html.twig\",{'accounts':toAccounts, 'type' :'credit'})}}

{% endblock %}

{% block javascripts %}
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>

    <script>
        jQuery(function (\$) {
            \$creditAccountOwner = \$('[id\$=\"toAccount_owner\"]');    
            \$creditAccountId = \$('[id\$=\"toAccount_id\"]');    
           
            \$debitAccountOwner = \$('[id\$=\"fromAccount_owner\"]');    
            \$debitAccountId = \$('[id\$=\"fromAccount_id\"]');    

            \$containerAccount = \$('.account'); 
            \$containerAccount.click(function (e){
                if(\$(this).attr('id') == 'account_credit'){
                    \$creditAccountOwner.val(\$(this).children(\"em\")[0].innerText) ;
                    \$creditAccountId.val(\$(this).children(\"span:last\")[0].innerText);
                }
                else{
                    \$debitAccountOwner.val(\$(this).children(\"em\")[0].innerText) ;
                    \$debitAccountId.val(\$(this).children(\"span:last\")[0].innerText);
                }
            });
        });
    </script>
{% endblock %}
", "CairnUserBundle:Banking:transaction.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/transaction.html.twig");
    }
}
