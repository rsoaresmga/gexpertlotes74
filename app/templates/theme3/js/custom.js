$(function() {
    // close side menu on small devices
    $('#side-menu a[generator="adianti"]').click(function() {
        $('body').removeClass('sidebar-open');
        $('body').scrollTop(0);
    })
    
    setTimeout( function() {
        $('#envelope_messages a').click(function() { $(this).closest('.dropdown.open').removeClass('open'); });
        $('#envelope_notifications a').click(function() { $(this).closest('.dropdown.open').removeClass('open'); });
    }, 500);
});

$( document ).on( 'click', 'ul.dropdown-menu a[generator="adianti"]', function() {
    $(this).parents(".dropdown.show").removeClass("show");
    $(this).parents(".dropdown-menu.show").removeClass("show");
});

function __adianti_init_tabs(t, i, a) {
    Adianti.tabs = {};
    Adianti.firstOpenTab = true;
    Adianti.useTabs = t;
    Adianti.storeTabs = i;
    Adianti.mdiWindows = a;
    Adianti.currentTab = null;
    if (Adianti.mdiWindows) {
        setTimeout(function() {
            $("#adianti_content .adianti_tabs_container").addClass("mdi-windows")
        }, 0)
    }
    if (Adianti.useTabs && Adianti.storeTabs) {
        var e = JSON.parse(localStorage.getItem("__adianti_tabs_" + Adianti.applicationName));
        
        var n = localStorage.getItem("__adianti_current_tab_" + Adianti.applicationName);
        if (e.length>0) {
            setTimeout(function() {
                $("#adianti_tab_content .adianti-tab").remove();
                if (n) {
                    Adianti.currentTab = n
                }
                for (var t of e) {
                    Adianti.tabs[t.name] = {
                        content: null,
                        id: t.uniqid,
                        name: t.name
                    };
            
                    __adianti_create_tab_item(t.uniqid, t.page, t.name, t.name == n)
                }
            }, 0);
            setTimeout(__adianti_scroll_to_active_tab, 250)
        }
    } else {
        localStorage.removeItem("__adianti_tabs_" + Adianti.applicationName);
        localStorage.removeItem("__adianti_current_tab_" + Adianti.applicationName)
    }
}

function __adianti_create_tab_item(t, i, a, e) {
    e = e ? "active" : "";
    $("#adianti_tab_content").append("<div onclick='__adianti_open_tab(\"" + i + '","' + a + "\")' id='" + t + "' class='adianti-tab " + e + "'><span class='adianti-tab-name'>" + a + "</span> <i onclick='__adianti_close_tab(\"" + a + "\", event); return false;' class='fas fa-times adianti-close-tab'></i> </div>")
}
function __adianti_set_current_tab(t) {
    Adianti.currentTab = t;
    if (Adianti.storeTabs) {
        localStorage.setItem("__adianti_current_tab_" + Adianti.applicationName, t)
    }
}
function __adianti_store_tab_content(t, i, a, e) {
    Adianti.tabs[e] = {
        content: a,
        id: i,
        name: e
    };
    if (Adianti.storeTabs) {
        var n = JSON.parse(localStorage.getItem("__adianti_tabs_" + Adianti.applicationName));
        if (!n) {
            n = []
        }
        var o = n.filter(t=>t.name == e).length > 0;
        if (!o) {
            n.push({
                page: t,
                name: e,
                uniqid: i
            });
            localStorage.setItem("__adianti_tabs_" + Adianti.applicationName, JSON.stringify(n))
        }
    }
}
function __adianti_open_tab(t, i) {
    if (Adianti.mdiWindows) {
        if (Adianti.currentTab != i) {
            $("#adianti_tab_content").find(".adianti-tab").removeClass("active");
            $("#" + Adianti.tabs[i].id).addClass("active");
            if (!Adianti.tabs[i].content && !Adianti.firstOpenTab) {
                __adianti_load_page(t + "&adianti_reload_tab=1")
            } else if (Adianti.tabs[i].content && !Adianti.tabs[i].content.is(":visible")) {
                __adianti_show_iframe(i)
            }
            __adianti_register_state(t)
        }
        __adianti_set_active_iframe(i)
    } else if (Adianti.currentTab != i) {
        if (typeof Adianti.tabs[Adianti.currentTab] != "undefined") {
            Adianti.tabs[Adianti.currentTab].content = $("#adianti_div_content").children().detach()
        }
        $("#adianti_tab_content").find(".adianti-tab").removeClass("active");
        $("#" + Adianti.tabs[i].id).addClass("active");
        if (!Adianti.tabs[i].content) {
            __adianti_load_page(t + "&adianti_reload_tab=1")
        } else {
            $("#adianti_div_content").html(Adianti.tabs[i].content);
            __adianti_register_state(t)
        }
    }
    __adianti_set_current_tab(i)
}
function __adianti_close_tab(i, t) {
    if (typeof t != "undefined") {
        t.preventDefault();
        t.stopPropagation()
    }
    $("#" + Adianti.tabs[i].id).remove();
    if (Adianti.currentTab == i) {
        $("#adianti_div_content").empty();
        let t = $("#adianti_tab_content").find(".adianti-tab-name");
        if (t.length > 0) {
            $(t[0]).click()
        }
    }
    if (Adianti.storeTabs) {
        var a = JSON.parse(localStorage.getItem("__adianti_tabs_" + Adianti.applicationName));
        a = a.filter(t=>t.name != i);
        localStorage.setItem("__adianti_tabs_" + Adianti.applicationName, JSON.stringify(a))
    }
    delete Adianti.tabs[i];
    if (Adianti.mdiWindows) {
        __adianti_close_iframe(i)
    }
}
function __adianti_add_tab(i, a) {
    if (typeof Adianti.tabs[a] == "undefined") {
        let t = parseInt(Math.random() * 1e8);
        $("#adianti_tab_content").find(".adianti-tab").removeClass("active");
        __adianti_create_tab_item(t, i, a, true);
        __adianti_store_tab_content(i, t, null, a);
        if (typeof Adianti.tabs[Adianti.currentTab] !== "undefined") {
            if (Adianti.mdiWindows) {
                Adianti.tabs[Adianti.currentTab].content = __adianti_get_iframe(Adianti.currentTab)
            } else {
                Adianti.tabs[Adianti.currentTab].content = $("#adianti_div_content").children().detach()
            }
        }
        if (Adianti.mdiWindows) {
            Adianti.tabs[a].content = __adianti_get_iframe(a)
        }
    } else if (Adianti.currentTab != a) {
        __adianti_open_tab(i, a)
    }
    setTimeout(__adianti_scroll_to_active_tab, 0);
    __adianti_set_current_tab(a)
}