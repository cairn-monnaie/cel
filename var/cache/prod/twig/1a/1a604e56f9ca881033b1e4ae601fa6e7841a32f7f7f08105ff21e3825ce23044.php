<?php

/* CairnUserBundle:Pdf:rib_cairn.html.twig */
class __TwigTemplate_7fd6600ec625fc9ac8450d25474242e9a752180bcb2c8096586aa32e03f89799 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout-pdf.html.twig", "CairnUserBundle:Pdf:rib_cairn.html.twig", 3);
        $this->blocks = array(
            'fos_user_content' => array($this, 'block_fos_user_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserBundle::layout-pdf.html.twig";
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
    <table>
        <thead>
            <tr>
                <th> Nom du compte </th>
                <th> Identifiant </th>
                <th> Devise </th> 
            </tr>
        </thead>
        <tbody>
             <tr>
                <td> ";
        // line 17
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "type", array()), "name", array()), "html", null, true);
        echo " </td>
                <td> ";
        // line 18
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "id", array()), "html", null, true);
        echo " </td>
                <td> ";
        // line 19
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "currency", array()), "suffix", array()), "html", null, true);
        echo " </td>
            </tr>
            <tr> 
                ";
        // line 22
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "type", array()), "nature", array()) == "SYSTEM")) {
            // line 23
            echo "                    ";
            $context["name"] = "Cairn, Monnaie Locale Complémentaire";
            // line 24
            echo "                ";
        } else {
            // line 25
            echo "                    ";
            $context["name"] = twig_get_attribute($this->env, $this->source, ($context["owner"] ?? null), "name", array());
            // line 26
            echo "                ";
        }
        // line 27
        echo "                    <div> <strong> Titulaire du compte </strong> </div>
                    <div> ";
        // line 28
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo " </div>
                    <div> ";
        // line 29
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["owner"] ?? null), "address", array()), "street", array()), "html", null, true);
        echo " </div>
                    <div> ";
        // line 30
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["owner"] ?? null), "city", array()), "html", null, true);
        echo " </div>
            </tr>
        </tbody>
    </table>

";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Pdf:rib_cairn.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  87 => 30,  83 => 29,  79 => 28,  76 => 27,  73 => 26,  70 => 25,  67 => 24,  64 => 23,  62 => 22,  56 => 19,  52 => 18,  48 => 17,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Pdf:rib_cairn.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pdf/rib_cairn.html.twig");
    }
}
