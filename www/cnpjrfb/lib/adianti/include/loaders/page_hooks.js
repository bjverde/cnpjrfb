function __adianti_run_after_loads(url, result)
{
    if (typeof Adianti.onAfterLoad == "function") {
        Adianti.onAfterLoad(url, result);
    }

    if (typeof Template.onAfterLoad == "function") {
        Template.onAfterLoad(url, result);
    }
    Adianti.loading = false;
}

function __adianti_run_after_posts(url, result)
{
    if (typeof Adianti.onAfterPost == "function") {
        Adianti.onAfterPost(url, result);
    }

    if (typeof Template.onAfterPost == "function") {
        Template.onAfterPost(url, result);
    }
    
    Adianti.loading = false;
}

function __adianti_run_before_loads(url)
{
    if (typeof Adianti.onBeforeLoad == "function") {
        Adianti.onBeforeLoad(url);
    }

    if (typeof Template.onBeforeLoad == "function") {
        Template.onBeforeLoad(url);
    }
    
    Adianti.loading = true;
}

function __adianti_run_before_posts(url)
{
    if (typeof Adianti.onBeforePost == "function") {
        Adianti.onBeforePost(url);
    }

    if (typeof Template.onBeforePost == "function") {
        Template.onBeforePost(url);
    }
    
    Adianti.loading = true;
}