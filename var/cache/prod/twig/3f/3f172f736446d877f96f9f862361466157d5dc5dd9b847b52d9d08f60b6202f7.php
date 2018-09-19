<?php

/* CairnUserBundle:Pro:list_beneficiaries.html.twig */
class __TwigTemplate_4de2c2af378b4e54c11727fc81102c3f3bbac3bed116cd4a09f7fd718033b358 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Pro:list_beneficiaries.html.twig", 3);
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
     <h2> Espace Professionnel ";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "name", array()), "html", null, true);
        echo "</h2>                                                  
    <h3> ";
        // line 15
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "d - m -Y"), "html", null, true);
        echo " </h3>                                                                               

    <div class=\"body_wrapper\">
        <div id=\"table_upper_block\">
            <p> Mes bénéficiaires </p>
            <div class=\"a_bloc_actions\">
                <ul>    
                    <li><a href=\"";
        // line 22
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_beneficiaries_add");
        echo "\">Ajouter </a></li>
                </ul>
            </div>
        </div>
        <table>                                                                    
             <thead>                                                                    
                 <tr>                                                                   
                     <th> Nom </th>                                                    
                     <th> Coordonnées de compte</th>                                          
                     <th> Actions </th>                                                 
                 </tr>                                                                  
             </thead>                                                                   
                                                                                        
             <tbody>                                                                    
             ";
        // line 36
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["beneficiaries"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["beneficiary"]) {
            echo "                                      
                 <tr>                                                                   
                     <td>";
            // line 38
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["beneficiary"], "user", array()), "name", array()), "html", null, true);
            echo " </td>                                    
                     <td>";
            // line 39
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["beneficiary"], "ICC", array()), "html", null, true);
            echo "</td>                                                              
                     <td><a href=\"";
            // line 40
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_beneficiaries_remove", array("id" => twig_get_attribute($this->env, $this->source, $context["beneficiary"], "id", array()))), "html", null, true);
            echo "\"> Supprimer</a> 
                         |
                         <a href=\"";
            // line 42
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_beneficiaries_edit", array("id" => twig_get_attribute($this->env, $this->source, $context["beneficiary"], "id", array()))), "html", null, true);
            echo "\"> Modifier</a>           
                    </td>                                 
                 </tr>                                                                  
             ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['beneficiary'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 45
        echo "                                                               
             </tbody>                                                                   
        </table>    
    </div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Pro:list_beneficiaries.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  122 => 45,  112 => 42,  107 => 40,  103 => 39,  99 => 38,  92 => 36,  75 => 22,  65 => 15,  61 => 14,  56 => 13,  53 => 12,  47 => 9,  42 => 8,  39 => 7,  34 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Pro:list_beneficiaries.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pro/list_beneficiaries.html.twig");
    }
}
