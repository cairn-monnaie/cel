<?php

/* CairnUserBundle:Admin/Users:index.html.twig */
class __TwigTemplate_3e104b9604b3ca043b0f25e85203f9ff48a85977ae07ddd6aa31830aa1146f91 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Admin/Users:index.html.twig", 3);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
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
    public function block_title($context, array $blocks = array())
    {
    }

    // line 7
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 8
        echo "    <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("layout-style.css"), "html", null, true);
        echo "\" type=\"text/css\" /> 
    <link rel=\"stylesheet\" href=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("pro.css"), "html", null, true);
        echo "\" type=\"text/css\" /> 

";
    }

    // line 12
    public function block_body($context, array $blocks = array())
    {
        // line 13
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo " 
 <h2> Espace Administrateur ";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "name", array()), "html", null, true);
        echo "</h2>                                                  
<h3> ";
        // line 15
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "d - m -Y"), "html", null, true);
        echo " </h3>                                                                               
<div>                                                                          
    <ul>
        <li><a href=\"";
        // line 18
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_add");
        echo "\" >Ajouter un professionnel</a></li> 
        <li><a href=\"";
        // line 19
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_search");
        echo "\" >Rechercher un professionnel</a></li>
        <li><a href=\"";
        // line 20
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_block_all");
        echo "\" >Fermer l'accès à la plateforme</a></li>
        <li><a href=\"";
        // line 21
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_activate_all");
        echo "\" >Ouvrir l'accès à la plateforme</a></li>
    </ul>
    <div>
        <ul>
            ";
        // line 25
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["listUsers"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["user"]) {
            // line 26
            echo "               <li><a href=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_profile_view", array("id" => twig_get_attribute($this->env, $this->source, $context["user"], "id", array()))), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "name", array()), "html", null, true);
            echo "</a></li>
            ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 27
            echo "  
                Pas <em>encore</em> d'utilisateur !
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['user'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 30
        echo "        <ul>
    </div>
</div>                                                                         
                                                                            
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Admin/Users:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  114 => 30,  106 => 27,  96 => 26,  91 => 25,  84 => 21,  80 => 20,  76 => 19,  72 => 18,  66 => 15,  62 => 14,  57 => 13,  54 => 12,  47 => 9,  42 => 8,  39 => 7,  34 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Admin/Users:index.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Admin/Users/index.html.twig");
    }
}
