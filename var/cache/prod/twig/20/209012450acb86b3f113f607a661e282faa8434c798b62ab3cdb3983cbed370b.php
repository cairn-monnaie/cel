<?php

/* CairnUserBundle:Card:generate_card.html.twig */
class __TwigTemplate_22aa731cc388f4aeed5211400af5aa5ddfade25a0d2a96b3a380c7f65cee6be2 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Card:generate_card.html.twig", 3);
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
    <strong>Attention, générer la carte de sécurité ne peut être effectué qu'une seule fois car il s'agit d'un document sensible. Assurez-vous d'imprimer le fichier téléchargé au plus vite. Autrement, ";
        // line 7
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "name", array()), "html", null, true);
        echo " devra déclarer sa nouvelle carte comme révoquée, et en commander une nouvelle. </strong>
    <div>                                                                          
        <a href=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_card_generate", array("id" => twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "id", array()))), "html", null, true);
        echo "\"> Générer la carte de sécurité de ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "name", array()), "html", null, true);
        echo " maintenant</a>
    </div>                                                                         

";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Card:generate_card.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 9,  40 => 7,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Card:generate_card.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Card/generate_card.html.twig");
    }
}
