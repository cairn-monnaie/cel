<?php

/* TwigBundle:Exception:error.html.twig */
class __TwigTemplate_9d1d0e14aef72a7309513d26c49cab7c7bc6b452aa6ad377aa398e06f51a4972 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 2
        $this->parent = $this->loadTemplate("layout.html.twig", "TwigBundle:Exception:error.html.twig", 2);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
            'aftercontainer' => array($this, 'block_aftercontainer'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_content($context, array $blocks = array())
    {
        // line 5
        echo "    <h3 class=\"header center\">Oups, quelque chose a mal tourné !</h3>
    <h4>erreur ";
        // line 6
        echo twig_escape_filter($this->env, ($context["status_code"] ?? null), "html", null, true);
        echo " : ";
        echo twig_escape_filter($this->env, ($context["status_text"] ?? null), "html", null, true);
        echo "</h4>

    ";
        // line 8
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("IS_AUTHENTICATED_FULLY")) {
            // line 9
            echo "
    ";
        }
        // line 11
        echo "    <p>
        N'hésite pas à envoyer un courriel à <a href=\"mailto:membres@lelefan.org?subject=error_";
        // line 12
        echo twig_escape_filter($this->env, ($context["status_code"] ?? null), "html", null, true);
        echo "_";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "h:i-d/m/Y"), "html", null, true);
        echo "\">membres@lelefan.org</a> pour expliquer les circonstances de ce bug... Merci !
    </p>
";
    }

    // line 16
    public function block_aftercontainer($context, array $blocks = array())
    {
        // line 17
        echo "    <div class=\"center-align\">
        <img class=\"responsive-img\" src=\"";
        // line 18
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("purple-unknown.png"), "html", null, true);
        echo "\" alt=\"Logo utilisateur\" /> 
    </div>
";
    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:error.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  70 => 18,  67 => 17,  64 => 16,  55 => 12,  52 => 11,  48 => 9,  46 => 8,  39 => 6,  36 => 5,  33 => 4,  15 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "TwigBundle:Exception:error.html.twig", "/var/www/Symfony/CairnB2B/app/Resources/TwigBundle/views/Exception/error.html.twig");
    }
}
