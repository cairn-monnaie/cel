<?php

/* CairnUserBundle:Banking:account_operations.html.twig */
class __TwigTemplate_a500f94fb16b3c8172b7e176f95fb34801aa52b8d3c5527968449a4c26c1dbcd extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:account_operations.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
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
        echo twig_include($this->env, $context, "CairnUserBundle:Banking:account_download_options.html.twig", array("account" => ($context["account"] ?? null)));
        echo "

    <div class=\"well\">
        ";
        // line 11
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_start');
        echo "
        ";
        // line 12
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'rest');
        echo "
        ";
        // line 13
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_end');
        echo "
    </div>
<div>
    <table>
    <caption> <span> Solde au ";
        // line 17
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "d-m-Y"), "html", null, true);
        echo " : </span> <span> ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "status", array()), "balance", array()), "html", null, true);
        echo " cairns </span> </br>
              <span> Dont opérations à venir : ";
        // line 18
        echo twig_escape_filter($this->env, ($context["futureAmount"] ?? null), "html", null, true);
        echo " cairns </span>

     </caption>
    
    <thead>
        <tr>
            <th> Date </th>
            <th> Opération </th> 
            <th> Montant </th>
            <th> Action </th>
  
        </tr>
    </thead>

    <tbody>
    ";
        // line 33
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["transactions"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["transaction"]) {
            // line 34
            echo "        <tr>
            <td>";
            // line 35
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "date", array()), "html", null, true);
            echo "</td>
            <td> ";
            // line 36
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array()), "html", null, true);
            echo " </td> 
            <td> ";
            // line 37
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()), "html", null, true);
            echo " </td>
            <td></td>
        </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transaction'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 41
        echo "    </tbody>
    </table>
</div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:account_operations.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  111 => 41,  101 => 37,  97 => 36,  93 => 35,  90 => 34,  86 => 33,  68 => 18,  62 => 17,  55 => 13,  51 => 12,  47 => 11,  41 => 8,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:account_operations.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/account_operations.html.twig");
    }
}
