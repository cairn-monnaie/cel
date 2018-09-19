<?php

/* SwiftmailerBundle:Collector:swiftmailer.html.twig */
class __TwigTemplate_98a1ecee2fc586a45ac64ca2aee57252dd8ed5c0bc7c2ad215ad432cdd8e7ba2 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "SwiftmailerBundle:Collector:swiftmailer.html.twig", 1);
        $this->blocks = array(
            'toolbar' => array($this, 'block_toolbar'),
            'menu' => array($this, 'block_menu'),
            'panel' => array($this, 'block_panel'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@WebProfiler/Profiler/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        $context["profiler_markup_version"] = (((isset($context["profiler_markup_version"]) || array_key_exists("profiler_markup_version", $context))) ? (_twig_default_filter(($context["profiler_markup_version"] ?? null), 1)) : (1));
        // line 5
        echo "
    ";
        // line 6
        if (twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array())) {
            // line 7
            echo "        ";
            ob_start();
            // line 8
            echo "            ";
            if ((($context["profiler_markup_version"] ?? null) == 1)) {
                // line 9
                echo "                <img width=\"23\" height=\"28\" alt=\"Swiftmailer\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAcCAYAAACK7SRjAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBNYWNpbnRvc2giIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6N0NEOTU1MjM0OThFMTFFMDg3NzJBNTE2ODgwQzMxMzQiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6N0NEOTU1MjQ0OThFMTFFMDg3NzJBNTE2ODgwQzMxMzQiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoxMEQ5OTQ5QzQ5OEMxMUUwODc3MkE1MTY4ODBDMzEzNCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo3Q0Q5NTUyMjQ5OEUxMUUwODc3MkE1MTY4ODBDMzEzNCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PpkRnSAAAAJ0SURBVHjaYvz//z8DrQATAw3BqOFYAaO9vT1FBhw4cGCAXA5MipxBQUHT3r17l0AVAxkZ/wkLC89as2ZNIcjlYkALXKnlWqBZTH/+/PEDmQsynLW/v3+NoaHhN2oYDjJn8uTJK4BMNpDhPwsLCwOKiop2+fn5vafEYC8vrw8gc/Lz8wOB3B8gw/nev38vn5WV5eTg4LA/Ly/vESsrK2npmYmJITU19SnQ8L0gc4DxpwgyF2S4EEjB58+f+crLy31YWFjOt7S0XBYUFPxHjMEcHBz/6+rqboqJiZ0qKSnxBpkDlRICGc4MU/j792+2CRMm+L18+fLSxIkTDykoKPzBZ7CoqOi/np6eE8rKylvb29v9fvz4wYEkzYKRzjk5OX/LyMjcnDRpEkjjdisrK6wRraOj8wvokAMLFy788ejRoxcaGhrPCWai4ODgB8DUE3/mzBknYMToASNoMzAfvEVW4+Tk9LmhoWFbTU2NwunTpx2BjiiMjo6+hm4WCzJHUlLyz+vXrxkfP36sDOI/ffpUPikpibe7u3sLsJjQW7VqlSrQxe+Avjmanp7u9PbtWzGQOmCCkARmmu/m5uYfT548yY/V5UpKSl+2b9+uiiz26dMnIWBSDTp27NgdYGrYCIzwE7m5uR4wg2Hg/PnzSsDI/QlKOcjZ3wGUBLm5uf+DwLdv38gub4AG/xcSEvr35s0bZmCB5sgCE/z69SsjyDJKMtG/f/8YQQYD8wkoGf8H51AbG5sH1CpbQBnQ09PzBiiHgoIFFHlBQGwLxLxUMP8dqJgH4k3gIhfIkAKVYkDMTmmhCHIxEL8A4peMo02L4WU4QIABANxZAoDIQDv3AAAAAElFTkSuQmCC\" />
                <span class=\"sf-toolbar-status\">";
                // line 10
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array()), "html", null, true);
                echo "</span>
            ";
            } else {
                // line 12
                echo "                ";
                echo twig_include($this->env, $context, "@Swiftmailer/Collector/icon.svg");
                echo "
                <span class=\"sf-toolbar-value\">";
                // line 13
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array()), "html", null, true);
                echo "</span>
            ";
            }
            // line 15
            echo "        ";
            $context["icon"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 16
            echo "
        ";
            // line 17
            ob_start();
            // line 18
            echo "            <div class=\"sf-toolbar-info-piece\">
                <b>Sent messages</b>
                <span class=\"sf-toolbar-status\">";
            // line 20
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array()), "html", null, true);
            echo "</span>
            </div>

            ";
            // line 23
            if ((($context["profiler_markup_version"] ?? null) == 1)) {
                // line 24
                echo "                ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "mailers", array()));
                $context['loop'] = array(
                  'parent' => $context['_parent'],
                  'index0' => 0,
                  'index'  => 1,
                  'first'  => true,
                );
                if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                    $length = count($context['_seq']);
                    $context['loop']['revindex0'] = $length - 1;
                    $context['loop']['revindex'] = $length;
                    $context['loop']['length'] = $length;
                    $context['loop']['last'] = 1 === $length;
                }
                foreach ($context['_seq'] as $context["_key"] => $context["name"]) {
                    // line 25
                    echo "                    <div class=\"sf-toolbar-info-piece\">
                        <b>";
                    // line 26
                    echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                    echo "</b>
                        <span>";
                    // line 27
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array(0 => $context["name"]), "method"), "html", null, true);
                    echo "</span>
                    </div>
                    <div class=\"sf-toolbar-info-piece\">
                        <b>Is spooled?</b>
                        <span>";
                    // line 31
                    echo ((twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "isSpool", array(0 => $context["name"]), "method")) ? ("yes") : ("no"));
                    echo "</span>
                    </div>

                    ";
                    // line 34
                    if ( !twig_get_attribute($this->env, $this->source, $context["loop"], "first", array())) {
                        // line 35
                        echo "                        <hr>
                    ";
                    }
                    // line 37
                    echo "                ";
                    ++$context['loop']['index0'];
                    ++$context['loop']['index'];
                    $context['loop']['first'] = false;
                    if (isset($context['loop']['length'])) {
                        --$context['loop']['revindex0'];
                        --$context['loop']['revindex'];
                        $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                    }
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['name'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 38
                echo "            ";
            } else {
                // line 39
                echo "                ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "mailers", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["name"]) {
                    // line 40
                    echo "                    <div class=\"sf-toolbar-info-piece\">
                        <b>";
                    // line 41
                    echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                    echo " mailer</b>
                        <span class=\"sf-toolbar-status\">";
                    // line 42
                    echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array(0 => $context["name"]), "method", true, true)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array(0 => $context["name"]), "method"), 0)) : (0)), "html", null, true);
                    echo "</span>
                        &nbsp; (<small>";
                    // line 43
                    echo ((twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "isSpool", array(0 => $context["name"]), "method")) ? ("spooled") : ("sent"));
                    echo "</small>)
                    </div>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['name'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 46
                echo "            ";
            }
            // line 47
            echo "        ";
            $context["text"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
            // line 48
            echo "
        ";
            // line 49
            echo twig_include($this->env, $context, "@WebProfiler/Profiler/toolbar_item.html.twig", array("link" => ($context["profiler_url"] ?? null)));
            echo "
    ";
        }
    }

    // line 53
    public function block_menu($context, array $blocks = array())
    {
        // line 54
        echo "    ";
        $context["profiler_markup_version"] = (((isset($context["profiler_markup_version"]) || array_key_exists("profiler_markup_version", $context))) ? (_twig_default_filter(($context["profiler_markup_version"] ?? null), 1)) : (1));
        // line 55
        echo "
    <span class=\"label ";
        // line 56
        echo ((twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array())) ? ("") : ("disabled"));
        echo "\">
        ";
        // line 57
        if ((($context["profiler_markup_version"] ?? null) == 1)) {
            // line 58
            echo "            <span class=\"icon\"><img src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACoAAAAeCAYAAABaKIzgAAAEDElEQVR42u2XWUhUURjHy6isILAebO+lonpJ8qkHM3NFVFxAUVFM0RSXUKnwwdAQn3wQE0MyA1MwBEUQcxvHZTTTHNcZl9DUGqd0JBOzcsZ7+n8XJ0Z0GueOVwg68GfmnLn3O7/7Lee7s4cxZpHq6uos0bb3+Q+6DrG3u7v7RHt7u3tbW9uD5ubm8qamJnlDQ4NKIpG8HhkZOU3X7BYoD9Tb22sjk8mcWltb70ul0pcAegugBfzOjKmzs/MJ7j0kCujw8PBhADkAKAVAz+EZGYA+08bmCN79qdFo7sGmjaWg+wgIIUtoaWl5CqDmxsbGj7SJpYK3uYWFBRnsDmEfWyGg+zs6OkIBNEoGxVB9fT2bnZ1VIHff03xmZuY29rUyF9QWT6sWC5I0OTk5rVAo3unnSqXyEfa1Nhf0Kp5UKRYk8lsDD0oM1/r6+l5h32Pmgl5UqVTP5ubmlEgBHRlCobC8vDzm5eXFHB0dRZWzs/OXsLCwu5SCpkBPo4DaMVRI9rbp6Wk1vnP+/v5kaFfk4eGhAcdJU6Dn+/v7q9aTnpPL5YqVlRV5eXm5Fk+7Gx7lCgsL68Fx2RToWST7C8McQgr8yMrKWkLu/hQz/LDNIZojycnJb8BxwRTocYT8oSEoQs8bSkpK0k5MTGgiIiK4nYYMDg7mcBLIo6OjP9Ec44opUGvIF+eoTg/a1dX1x2BISMgyKncpLS1tbacgU1NT2djY2HxoaOi8fg3jhilQK+gmQvBVD4qQbzDs4+ND6bBWUFCgtRQyJyeHdwSKdcODY9zaTgu9jheMcWOgJFdXV1ZZWclqamp0bm5uQoqGVVRUrFHBuru782tCQC+hW0j/BkpCPlGXIY9wfn5+5hQN5T3dS+cz5btg0DNTU1NFpkBra2tZaWkpy8jIYOPj4ywmJmY7RcMGBwdZZmYmKykpoa7ELPGozeLiYrIetKenZ5OhuLg4ft3T05OfJyQk8LDp6el/LRq6JiUlheb8vXgzY7m5uYJBD0LeeDnRApQ8sKloqK3GxsZuWEPrYzhiWHFx8ZZFMzo6yiIjIw1DTTZ+qNXqMRcXF0GgVpADKltDoCisDcbj4+NZfn7+ll5D9fKeprYbFRXFwsPDWVVV1SodPwEBAVueEtnZ2QNIhTkhoKRrAxhb5WhRURFzcnIyGmIcX9rq6uoPq6urAzqdrresrGwIHtMZux62OOT6AD4FgZ5bXl5+DMhv6P16j/KhCwoK2vHO5O3t/SsxMfG7ENAjkAOUBUkMvMVDiiFKDSGge6Gj0CUoGmffpvwSEfg7dUch/0LtkWcdvr6+a2JDBgYG6tDt6DXPTgjoKegOVAo1QVKR1AgVQ8GQrRDQA+uw9ushuSWSyLYdQRr7K/JP6DcTwr8i7Fj8pwAAAABJRU5ErkJggg==\" alt=\"Swiftmailer\" /></span>
        ";
        } else {
            // line 60
            echo "            <span class=\"icon\">";
            echo twig_include($this->env, $context, "@Swiftmailer/Collector/icon.svg");
            echo "</span>
        ";
        }
        // line 62
        echo "
        <strong>E-Mails</strong>
        ";
        // line 64
        if ((twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array()) > 0)) {
            // line 65
            echo "            <span class=\"count\">
                <span>";
            // line 66
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array()), "html", null, true);
            echo "</span>
            </span>
        ";
        }
        // line 69
        echo "    </span>
";
    }

    // line 72
    public function block_panel($context, array $blocks = array())
    {
        // line 73
        echo "    ";
        $context["profiler_markup_version"] = (((isset($context["profiler_markup_version"]) || array_key_exists("profiler_markup_version", $context))) ? (_twig_default_filter(($context["profiler_markup_version"] ?? null), 1)) : (1));
        // line 74
        echo "
    ";
        // line 75
        if ((($context["profiler_markup_version"] ?? null) == 1)) {
            // line 76
            echo "        <style>
            h3 { margin-top: 2em; }
            h3 span { color: #999; font-weight: normal; }
            h3 small { color: #999; }
            h4 { font-size: 14px; font-weight: bold; }
            .card { background: #F5F5F5; margin: .5em 0 1em; padding: .5em; }
            .card .label { display: block; font-size: 13px; font-weight: bold; margin-bottom: .5em; }
            .card .card-block { margin-bottom: 1em; }
        </style>
    ";
        }
        // line 86
        echo "
    <h2>E-mails</h2>

    ";
        // line 89
        if ( !twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "mailers", array())) {
            // line 90
            echo "        <div class=\"empty\">
            <p>No e-mail messages were sent.</p>
        </div>
    ";
        }
        // line 94
        echo "
    ";
        // line 95
        if (((($context["profiler_markup_version"] ?? null) == 1) || (twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "mailers", array())) > 1))) {
            // line 96
            echo "        <table>
            <thead>
                <tr>
                    <th scope=\"col\">Mailer Name</th>
                    <th scope=\"col\">Num. of messages</th>
                    <th scope=\"col\">Messages status</th>
                    <th scope=\"col\">Notes</th>
                </tr>
            </thead>
            <tbody>
                ";
            // line 106
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "mailers", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["name"]) {
                // line 107
                echo "                    <tr>
                        <th class=\"font-normal\">";
                // line 108
                echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                echo "</th>
                        <td class=\"font-normal\">";
                // line 109
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array(0 => $context["name"]), "method"), "html", null, true);
                echo "</td>
                        <td class=\"font-normal\">";
                // line 110
                echo ((twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "isSpool", array(0 => $context["name"]), "method")) ? ("spooled") : ("sent"));
                echo "</td>
                        <td class=\"font-normal\">";
                // line 111
                echo ((twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "isDefaultMailer", array(0 => $context["name"]), "method")) ? ("This is the default mailer") : (""));
                echo "</td>
                    </tr>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['name'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 114
            echo "            </tbody>
        </table>
    ";
        } else {
            // line 117
            echo "        <div class=\"metrics\">
            ";
            // line 118
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "mailers", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["name"]) {
                // line 119
                echo "                <div class=\"metric\">
                    <span class=\"value\">";
                // line 120
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array(0 => $context["name"]), "method"), "html", null, true);
                echo "</span>
                    <span class=\"label\">";
                // line 121
                echo ((twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "isSpool", array(0 => $context["name"]), "method")) ? ("spooled") : ("sent"));
                echo " ";
                echo (((twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messageCount", array(0 => $context["name"]), "method") == 1)) ? ("message") : ("messages"));
                echo "</span>
                </div>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['name'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 124
            echo "        </div>
    ";
        }
        // line 126
        echo "
    ";
        // line 127
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "mailers", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["name"]) {
            // line 128
            echo "        ";
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "mailers", array())) > 1)) {
                // line 129
                echo "            <h3>
                ";
                // line 130
                echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                echo " <span>mailer</span>
                <small>";
                // line 131
                echo ((twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "isDefaultMailer", array(0 => $context["name"]), "method")) ? ("(default app mailer)") : (""));
                echo "</small>
            </h3>
        ";
            }
            // line 134
            echo "
        ";
            // line 135
            if ( !twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messages", array(0 => $context["name"]), "method")) {
                // line 136
                echo "            <div class=\"empty\">
                <p>No e-mail messages were sent.</p>
            </div>
        ";
            } else {
                // line 140
                echo "            ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "messages", array(0 => $context["name"]), "method"));
                $context['loop'] = array(
                  'parent' => $context['_parent'],
                  'index0' => 0,
                  'index'  => 1,
                  'first'  => true,
                );
                if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                    $length = count($context['_seq']);
                    $context['loop']['revindex0'] = $length - 1;
                    $context['loop']['revindex'] = $length;
                    $context['loop']['length'] = $length;
                    $context['loop']['last'] = 1 === $length;
                }
                foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
                    // line 141
                    echo "                ";
                    if ((twig_get_attribute($this->env, $this->source, $context["loop"], "length", array()) > 1)) {
                        // line 142
                        echo "                    <h4>E-mail #";
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["loop"], "index", array()), "html", null, true);
                        echo " details</h4>
                ";
                    } else {
                        // line 144
                        echo "                    <h4>E-mail details</h4>
                ";
                    }
                    // line 146
                    echo "
                <div class=\"card\">
                    <div class=\"card-block\">
                        <span class=\"label\">Message headers</span>
                        <pre>";
                    // line 150
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["message"], "headers", array()), "all", array()));
                    foreach ($context['_seq'] as $context["_key"] => $context["header"]) {
                        // line 151
                        echo twig_escape_filter($this->env, $context["header"], "html", null, true);
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['header'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 152
                    echo "</pre>
                    </div>

                    <div class=\"card-block\">
                        <span class=\"label\">Message body</span>
                        <pre>";
                    // line 158
                    if ((twig_get_attribute($this->env, $this->source, ($context["messagePart"] ?? null), "charset", array(), "any", true, true) && twig_get_attribute($this->env, $this->source, $context["message"], "charset", array()))) {
                        // line 159
                        echo twig_escape_filter($this->env, twig_convert_encoding(twig_get_attribute($this->env, $this->source, $context["message"], "body", array()), "UTF-8", twig_get_attribute($this->env, $this->source, $context["message"], "charset", array())), "html", null, true);
                    } else {
                        // line 161
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["message"], "body", array()), "html", null, true);
                    }
                    // line 163
                    echo "</pre>
                    </div>

                    ";
                    // line 166
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["message"], "children", array()));
                    foreach ($context['_seq'] as $context["_key"] => $context["messagePart"]) {
                        if (twig_in_filter(twig_get_attribute($this->env, $this->source, $context["messagePart"], "contentType", array()), array(0 => "text/plain", 1 => "text/html"))) {
                            // line 167
                            echo "                        <div class=\"card-block\">
                            <span class=\"label\">Alternative part (";
                            // line 168
                            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["messagePart"], "contentType", array()), "html", null, true);
                            echo ")</span>
                            <pre>";
                            // line 170
                            if ((twig_get_attribute($this->env, $this->source, $context["messagePart"], "charset", array(), "any", true, true) && twig_get_attribute($this->env, $this->source, $context["messagePart"], "charset", array()))) {
                                // line 171
                                echo twig_escape_filter($this->env, twig_convert_encoding(twig_get_attribute($this->env, $this->source, $context["messagePart"], "body", array()), "UTF-8", twig_get_attribute($this->env, $this->source, $context["messagePart"], "charset", array())), "html", null, true);
                            } else {
                                // line 173
                                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["messagePart"], "body", array()), "html", null, true);
                            }
                            // line 175
                            echo "</pre>
                        </div>
                    ";
                        }
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['messagePart'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 178
                    echo "
                    ";
                    // line 179
                    $context["attachments"] = twig_get_attribute($this->env, $this->source, ($context["collector"] ?? null), "extractAttachments", array(0 => $context["message"]), "method");
                    // line 180
                    echo "                    ";
                    if (($context["attachments"] ?? null)) {
                        // line 181
                        echo "                        <div class=\"card-block\">
                            <span class=\"label\">
                                ";
                        // line 183
                        if ((twig_length_filter($this->env, ($context["attachments"] ?? null)) > 1)) {
                            // line 184
                            echo "                                    ";
                            echo twig_escape_filter($this->env, twig_length_filter($this->env, ($context["attachments"] ?? null)), "html", null, true);
                            echo " Attachments
                                ";
                        } else {
                            // line 186
                            echo "                                    1 Attachment
                                ";
                        }
                        // line 188
                        echo "                            </span>

                            <ol>
                                ";
                        // line 191
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable(($context["attachments"] ?? null));
                        foreach ($context['_seq'] as $context["_key"] => $context["attachment"]) {
                            // line 192
                            echo "                                    <li>
                                        Filename:
                                        ";
                            // line 194
                            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["attachment"], "filename", array()), "html", null, true);
                            echo "
                                    </li>
                                ";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['attachment'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 197
                        echo "                            </ol>
                        </div>
                    ";
                    }
                    // line 200
                    echo "                </div>
            ";
                    ++$context['loop']['index0'];
                    ++$context['loop']['index'];
                    $context['loop']['first'] = false;
                    if (isset($context['loop']['length'])) {
                        --$context['loop']['revindex0'];
                        --$context['loop']['revindex'];
                        $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                    }
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['message'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 202
                echo "        ";
            }
            // line 203
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['name'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "SwiftmailerBundle:Collector:swiftmailer.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  550 => 203,  547 => 202,  532 => 200,  527 => 197,  518 => 194,  514 => 192,  510 => 191,  505 => 188,  501 => 186,  495 => 184,  493 => 183,  489 => 181,  486 => 180,  484 => 179,  481 => 178,  472 => 175,  469 => 173,  466 => 171,  464 => 170,  460 => 168,  457 => 167,  452 => 166,  447 => 163,  444 => 161,  441 => 159,  439 => 158,  432 => 152,  426 => 151,  422 => 150,  416 => 146,  412 => 144,  406 => 142,  403 => 141,  385 => 140,  379 => 136,  377 => 135,  374 => 134,  368 => 131,  364 => 130,  361 => 129,  358 => 128,  354 => 127,  351 => 126,  347 => 124,  336 => 121,  332 => 120,  329 => 119,  325 => 118,  322 => 117,  317 => 114,  308 => 111,  304 => 110,  300 => 109,  296 => 108,  293 => 107,  289 => 106,  277 => 96,  275 => 95,  272 => 94,  266 => 90,  264 => 89,  259 => 86,  247 => 76,  245 => 75,  242 => 74,  239 => 73,  236 => 72,  231 => 69,  225 => 66,  222 => 65,  220 => 64,  216 => 62,  210 => 60,  206 => 58,  204 => 57,  200 => 56,  197 => 55,  194 => 54,  191 => 53,  184 => 49,  181 => 48,  178 => 47,  175 => 46,  166 => 43,  162 => 42,  158 => 41,  155 => 40,  150 => 39,  147 => 38,  133 => 37,  129 => 35,  127 => 34,  121 => 31,  114 => 27,  110 => 26,  107 => 25,  89 => 24,  87 => 23,  81 => 20,  77 => 18,  75 => 17,  72 => 16,  69 => 15,  64 => 13,  59 => 12,  54 => 10,  51 => 9,  48 => 8,  45 => 7,  43 => 6,  40 => 5,  37 => 4,  34 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "SwiftmailerBundle:Collector:swiftmailer.html.twig", "/var/www/Symfony/CairnB2B/vendor/symfony/swiftmailer-bundle/Resources/views/Collector/swiftmailer.html.twig");
    }
}
