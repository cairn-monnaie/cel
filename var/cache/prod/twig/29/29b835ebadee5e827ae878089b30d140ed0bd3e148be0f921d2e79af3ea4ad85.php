<?php

/* CairnUserCyclosBundle:Config/AccountType:view.html.twig */
class __TwigTemplate_35ef90d8bb76608aa58fec7012319bbcef6d61a3b4ea6aa8dffd1d46f7e2f241 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/AccountType:view.html.twig", 3);
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
    
    <h2> ";
        // line 9
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "name", array()), "html", null, true);
        echo " </h2>
  <div class=\"well\">
    <div>
        ";
        // line 12
        if ((twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "nature", array()) == "USER")) {
            // line 13
            echo "            <li> Limite basse : ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["product"] ?? null), "defaultCreditLimit", array()), "html", null, true);
            echo "</li>
            <li> Etat :
                ";
            // line 15
            if (($context["isAssigned"] ?? null)) {
                // line 16
                echo "                      Ouvert
                ";
            } else {
                // line 18
                echo "                      Fermé
                ";
            }
            // line 20
            echo "
            </li>
         ";
            // line 22
            echo " 

        ";
        } else {
            // line 25
            echo "            <li>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "limitType", array()), "html", null, true);
            echo "</li>
            ";
            // line 26
            if ((twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "limitType", array()) == "LIMITED")) {
                // line 27
                echo "                <li>";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "creditLimit", array()), "html", null, true);
                echo "</li>
            ";
            }
            // line 29
            echo "        ";
        }
        // line 30
        echo "    </div>

        <h3> Types de transfert liés au compte</h3>
            <h4> Transferts vers types de comptes professionnels </h4>
                <table>
                    <tr>
                        <th>Vers le compte</th>
                        <th>Actif</th>
                      ";
        // line 39
        echo "                        <th>Action</th>

                    </tr>
                    ";
        // line 42
        if ((twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "nature", array()) == "USER")) {
            // line 43
            echo "                        ";
            $context["listTransferTypes"] = twig_get_attribute($this->env, $this->source, ($context["product"] ?? null), "userPayments", array());
            // line 44
            echo "                    ";
        } else {
            // line 45
            echo "                        ";
            $context["listTransferTypes"] = twig_get_attribute($this->env, $this->source, ($context["product"] ?? null), "systemToUserPayments", array());
            // line 46
            echo "                    ";
        }
        // line 47
        echo "                    ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["listTransferTypes"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["transferType"]) {
            // line 48
            echo "                        ";
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transferType"], "from", array()), "name", array()) == twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "name", array()))) {
                // line 49
                echo "
                            <tr>
                                <td><a href=\"";
                // line 51
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_view", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transferType"], "to", array()), "id", array()))), "html", null, true);
                echo "\"> ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transferType"], "to", array()), "name", array()), "html", null, true);
                echo "</a></td>
                                <td> ";
                // line 52
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transferType"], "enabled", array()), "html", null, true);
                echo " </td>
                              ";
                // line 54
                echo "                                <td><a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_transfertype_view", array("id" => twig_get_attribute($this->env, $this->source, $context["transferType"], "id", array()))), "html", null, true);
                echo "\">Voir</a></td>
    
                                </a>
                            </tr>
                        ";
            }
            // line 59
            echo "                    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 60
            echo "                        Pas de type de transfert associé
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transferType'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 62
        echo "                </table>

             <h4> Transferts vers types de comptes système </h4>
                <table>
                    <tr>
                        <th>Vers le compte</th>
                        <th>Actif</th>
                      ";
        // line 70
        echo "                    </tr>
                    ";
        // line 71
        if ((twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "nature", array()) == "USER")) {
            // line 72
            echo "                        ";
            $context["listTransferTypes"] = twig_get_attribute($this->env, $this->source, ($context["product"] ?? null), "systemPayments", array());
            // line 73
            echo "                    ";
        } else {
            // line 74
            echo "                        ";
            $context["listTransferTypes"] = twig_get_attribute($this->env, $this->source, ($context["product"] ?? null), "systemToSystemPayments", array());
            // line 75
            echo "                    ";
        }
        // line 76
        echo "
                    ";
        // line 77
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["listTransferTypes"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["transferType"]) {
            // line 78
            echo "                        ";
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transferType"], "from", array()), "name", array()) == twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "name", array()))) {
                // line 79
                echo "                            <tr>
                                <td><a href=\"";
                // line 80
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_view", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transferType"], "to", array()), "id", array()))), "html", null, true);
                echo "\"> ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transferType"], "to", array()), "name", array()), "html", null, true);
                echo "</a></td>
                                <td> ";
                // line 81
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transferType"], "enabled", array()), "html", null, true);
                echo " </td>
                               ";
                // line 83
                echo "                                <td><a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_transfertype_view", array("id" => twig_get_attribute($this->env, $this->source, $context["transferType"], "id", array()))), "html", null, true);
                echo "\">Voir</a></td>
                            </tr>
                        ";
            }
            // line 86
            echo "                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transferType'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 87
        echo "
                </table>
  </div>

  <p>
    <a href=\"";
        // line 92
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_list");
        echo "\">
      Retour à la liste des comptes
    </a>
     <a href=\"";
        // line 95
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_edit", array("id" => twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "id", array()))), "html", null, true);
        echo "\">
      Mettre à jour le type de compte
    </a>
    ";
        // line 98
        if ((twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "nature", array()) == "USER")) {
            // line 99
            echo "        ";
            if (($context["isAssigned"] ?? null)) {
                // line 100
                echo "             <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_remove_confirm", array("id" => twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "id", array()))), "html", null, true);
                echo "\">
              Fermer le type de compte
             </a>

        ";
            } else {
                // line 105
                echo "             <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_open_confirm", array("id" => twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "id", array()))), "html", null, true);
                echo "\">
              Ouvrir le type de compte
            </a>

        ";
            }
            // line 110
            echo "    ";
        }
        // line 111
        echo "
  </p>

";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/AccountType:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  270 => 111,  267 => 110,  258 => 105,  249 => 100,  246 => 99,  244 => 98,  238 => 95,  232 => 92,  225 => 87,  219 => 86,  212 => 83,  208 => 81,  202 => 80,  199 => 79,  196 => 78,  192 => 77,  189 => 76,  186 => 75,  183 => 74,  180 => 73,  177 => 72,  175 => 71,  172 => 70,  163 => 62,  156 => 60,  151 => 59,  142 => 54,  138 => 52,  132 => 51,  128 => 49,  125 => 48,  119 => 47,  116 => 46,  113 => 45,  110 => 44,  107 => 43,  105 => 42,  100 => 39,  90 => 30,  87 => 29,  81 => 27,  79 => 26,  74 => 25,  69 => 22,  65 => 20,  61 => 18,  57 => 16,  55 => 15,  49 => 13,  47 => 12,  41 => 9,  35 => 7,  32 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/AccountType:view.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/AccountType/view.html.twig");
    }
}
