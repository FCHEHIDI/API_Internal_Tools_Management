<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Internal Tools API API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../docs/css/theme-default.style.css" media="screen">
    <link rel="stylesheet" href="../docs/css/theme-default.print.css" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost:8000";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="../docs/js/tryitout-5.6.0.js"></script>

    <script src="../docs/js/theme-default-5.6.0.js"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="../docs/images/navbar.png" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="endpoints">
                    <a href="#endpoints">Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="endpoints-GETapi-tools">
                                <a href="#endpoints-GETapi-tools">Display a listing of tools with optional filters.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-tools">
                                <a href="#endpoints-POSTapi-tools">Store a newly created tool in storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-tools--id-">
                                <a href="#endpoints-GETapi-tools--id-">Display the specified tool with full details.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-tools--id-">
                                <a href="#endpoints-PUTapi-tools--id-">Update the specified tool in storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-tools--id-">
                                <a href="#endpoints-DELETEapi-tools--id-">Remove the specified tool from storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-analytics-department-costs">
                                <a href="#endpoints-GETapi-analytics-department-costs">Get cost breakdown by department</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-analytics-expensive-tools">
                                <a href="#endpoints-GETapi-analytics-expensive-tools">Get top expensive tools with efficiency analysis</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-analytics-tools-by-category">
                                <a href="#endpoints-GETapi-analytics-tools-by-category">Get tools distribution by category</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-analytics-low-usage-tools">
                                <a href="#endpoints-GETapi-analytics-low-usage-tools">Get low usage tools with potential savings</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-analytics-vendor-summary">
                                <a href="#endpoints-GETapi-analytics-vendor-summary">Get vendor summary and analysis</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="../docs/collection.json">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="../docs/openapi.yaml">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: November 27, 2025</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="endpoints">Endpoints</h1>

    

                                <h2 id="endpoints-GETapi-tools">Display a listing of tools with optional filters.</h2>

<p>
</p>

<p>Filters: department, status, category, min_cost, max_cost, search
Sorting: sort_by (name|monthly_cost|created_at), order (asc|desc)
Pagination: page, limit</p>

<span id="example-requests-GETapi-tools">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/tools" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tools"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-tools">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 78,
            &quot;name&quot;: &quot;Test Tool&quot;,
            &quot;description&quot;: &quot;A test tool&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;category&quot;: &quot;Communication&quot;,
            &quot;monthly_cost&quot;: 10.5,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: &quot;https://example.com&quot;,
            &quot;active_users_count&quot;: 0,
            &quot;created_at&quot;: &quot;2025-11-27T14:27:35.027642Z&quot;
        },
        {
            &quot;id&quot;: 76,
            &quot;name&quot;: &quot;Test Tool Delete 1764251909068&quot;,
            &quot;description&quot;: null,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;category&quot;: &quot;Communication&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 0,
            &quot;created_at&quot;: &quot;2025-11-27T13:58:29.068182Z&quot;
        },
        {
            &quot;id&quot;: 69,
            &quot;name&quot;: &quot;Test Tool Delete 1764251797614&quot;,
            &quot;description&quot;: null,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;category&quot;: &quot;Communication&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 0,
            &quot;created_at&quot;: &quot;2025-11-27T13:56:37.615230Z&quot;
        },
        {
            &quot;id&quot;: 62,
            &quot;name&quot;: &quot;Test Tool Delete 1764251672234&quot;,
            &quot;description&quot;: null,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;category&quot;: &quot;Communication&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 0,
            &quot;created_at&quot;: &quot;2025-11-27T13:54:32.235579Z&quot;
        },
        {
            &quot;id&quot;: 55,
            &quot;name&quot;: &quot;Test Tool Delete 1764250175735&quot;,
            &quot;description&quot;: null,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;category&quot;: &quot;Communication&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 0,
            &quot;created_at&quot;: &quot;2025-11-27T13:29:35.735377Z&quot;
        },
        {
            &quot;id&quot;: 48,
            &quot;name&quot;: &quot;Test Tool Delete 1764250165312&quot;,
            &quot;description&quot;: null,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;category&quot;: &quot;Communication&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 0,
            &quot;created_at&quot;: &quot;2025-11-27T13:29:25.324422Z&quot;
        },
        {
            &quot;id&quot;: 41,
            &quot;name&quot;: &quot;Test Tool Delete 1764249099225&quot;,
            &quot;description&quot;: null,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;category&quot;: &quot;Communication&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 0,
            &quot;created_at&quot;: &quot;2025-11-27T13:11:39.224805Z&quot;
        },
        {
            &quot;id&quot;: 34,
            &quot;name&quot;: &quot;Test Tool Delete 1764249012441&quot;,
            &quot;description&quot;: null,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;category&quot;: &quot;Communication&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 0,
            &quot;created_at&quot;: &quot;2025-11-27T13:10:12.442235Z&quot;
        },
        {
            &quot;id&quot;: 87860,
            &quot;name&quot;: &quot;Test Figma Pro&quot;,
            &quot;description&quot;: &quot;Design platform for testing&quot;,
            &quot;vendor&quot;: &quot;Figma&quot;,
            &quot;category&quot;: &quot;Test Design Tools 41941&quot;,
            &quot;monthly_cost&quot;: 12,
            &quot;owner_department&quot;: &quot;Design&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 15,
            &quot;created_at&quot;: &quot;2025-11-27T12:56:19.225058Z&quot;
        },
        {
            &quot;id&quot;: 58498,
            &quot;name&quot;: &quot;Test GitHub Enterprise&quot;,
            &quot;description&quot;: &quot;Source code management for testing&quot;,
            &quot;vendor&quot;: &quot;GitHub&quot;,
            &quot;category&quot;: &quot;Test Development Tools 41106&quot;,
            &quot;monthly_cost&quot;: 30,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 50,
            &quot;created_at&quot;: &quot;2025-11-27T12:56:19.095356Z&quot;
        },
        {
            &quot;id&quot;: 58499,
            &quot;name&quot;: &quot;Test Figma Pro&quot;,
            &quot;description&quot;: &quot;Design platform for testing&quot;,
            &quot;vendor&quot;: &quot;Figma&quot;,
            &quot;category&quot;: &quot;Test Design Tools 41106&quot;,
            &quot;monthly_cost&quot;: 12,
            &quot;owner_department&quot;: &quot;Design&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 15,
            &quot;created_at&quot;: &quot;2025-11-27T12:56:19.095356Z&quot;
        },
        {
            &quot;id&quot;: 16087,
            &quot;name&quot;: &quot;Test GitHub Enterprise&quot;,
            &quot;description&quot;: &quot;Source code management for testing&quot;,
            &quot;vendor&quot;: &quot;GitHub&quot;,
            &quot;category&quot;: &quot;Test Development Tools 95353&quot;,
            &quot;monthly_cost&quot;: 25,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;trial&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 50,
            &quot;created_at&quot;: &quot;2025-11-27T12:56:18.926756Z&quot;
        },
        {
            &quot;id&quot;: 16088,
            &quot;name&quot;: &quot;Test Figma Pro&quot;,
            &quot;description&quot;: &quot;Design platform for testing&quot;,
            &quot;vendor&quot;: &quot;Figma&quot;,
            &quot;category&quot;: &quot;Test Design Tools 95353&quot;,
            &quot;monthly_cost&quot;: 12,
            &quot;owner_department&quot;: &quot;Design&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 15,
            &quot;created_at&quot;: &quot;2025-11-27T12:56:18.926756Z&quot;
        },
        {
            &quot;id&quot;: 27,
            &quot;name&quot;: &quot;New Test Tool&quot;,
            &quot;description&quot;: &quot;A tool created by tests&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;category&quot;: &quot;Test Development Tools 43382&quot;,
            &quot;monthly_cost&quot;: 99.99,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 0,
            &quot;created_at&quot;: &quot;2025-11-27T12:56:18.828458Z&quot;
        },
        {
            &quot;id&quot;: 76514,
            &quot;name&quot;: &quot;Test Figma Pro&quot;,
            &quot;description&quot;: &quot;Design platform for testing&quot;,
            &quot;vendor&quot;: &quot;Figma&quot;,
            &quot;category&quot;: &quot;Test Design Tools 89993&quot;,
            &quot;monthly_cost&quot;: 12,
            &quot;owner_department&quot;: &quot;Design&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 15,
            &quot;created_at&quot;: &quot;2025-11-27T12:56:18.744052Z&quot;
        },
        {
            &quot;id&quot;: 76513,
            &quot;name&quot;: &quot;Test GitHub Enterprise&quot;,
            &quot;description&quot;: &quot;Source code management for testing&quot;,
            &quot;vendor&quot;: &quot;GitHub&quot;,
            &quot;category&quot;: &quot;Test Development Tools 89993&quot;,
            &quot;monthly_cost&quot;: 21,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 50,
            &quot;created_at&quot;: &quot;2025-11-27T12:56:18.744052Z&quot;
        },
        {
            &quot;id&quot;: 15719,
            &quot;name&quot;: &quot;Test Figma Pro&quot;,
            &quot;description&quot;: &quot;Design platform for testing&quot;,
            &quot;vendor&quot;: &quot;Figma&quot;,
            &quot;category&quot;: &quot;Test Design Tools 57191&quot;,
            &quot;monthly_cost&quot;: 12,
            &quot;owner_department&quot;: &quot;Design&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 15,
            &quot;created_at&quot;: &quot;2025-11-27T12:56:18.639579Z&quot;
        },
        {
            &quot;id&quot;: 15718,
            &quot;name&quot;: &quot;Test GitHub Enterprise&quot;,
            &quot;description&quot;: &quot;Source code management for testing&quot;,
            &quot;vendor&quot;: &quot;GitHub&quot;,
            &quot;category&quot;: &quot;Test Development Tools 57191&quot;,
            &quot;monthly_cost&quot;: 21,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 50,
            &quot;created_at&quot;: &quot;2025-11-27T12:56:18.639579Z&quot;
        },
        {
            &quot;id&quot;: 53366,
            &quot;name&quot;: &quot;Test Figma Pro&quot;,
            &quot;description&quot;: &quot;Design platform for testing&quot;,
            &quot;vendor&quot;: &quot;Figma&quot;,
            &quot;category&quot;: &quot;Test Design Tools 26841&quot;,
            &quot;monthly_cost&quot;: 12,
            &quot;owner_department&quot;: &quot;Design&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 15,
            &quot;created_at&quot;: &quot;2025-11-27T12:56:18.572896Z&quot;
        },
        {
            &quot;id&quot;: 53365,
            &quot;name&quot;: &quot;Test GitHub Enterprise&quot;,
            &quot;description&quot;: &quot;Source code management for testing&quot;,
            &quot;vendor&quot;: &quot;GitHub&quot;,
            &quot;category&quot;: &quot;Test Development Tools 26841&quot;,
            &quot;monthly_cost&quot;: 21,
            &quot;owner_department&quot;: &quot;Engineering&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;website_url&quot;: null,
            &quot;active_users_count&quot;: 50,
            &quot;created_at&quot;: &quot;2025-11-27T12:56:18.572896Z&quot;
        }
    ],
    &quot;total&quot;: 138,
    &quot;filtered&quot;: 138,
    &quot;current_page&quot;: 1,
    &quot;last_page&quot;: 7,
    &quot;per_page&quot;: 20,
    &quot;filters_applied&quot;: []
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-tools" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-tools"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-tools"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-tools" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-tools">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-tools" data-method="GET"
      data-path="api/tools"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-tools', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-tools"
                    onclick="tryItOut('GETapi-tools');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-tools"
                    onclick="cancelTryOut('GETapi-tools');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-tools"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/tools</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-tools"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-tools"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-tools">Store a newly created tool in storage.</h2>

<p>
</p>



<span id="example-requests-POSTapi-tools">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/tools" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"vmqeopfuudtdsufvyvddq\",
    \"description\": \"Dolores dolorum amet iste laborum eius est dolor.\",
    \"vendor\": \"dtdsufvyvddqamniihfqc\",
    \"website_url\": \"http:\\/\\/www.weimann.com\\/perferendis-voluptatibus-incidunt-nostrum-quia-possimus.html\",
    \"category_id\": 17,
    \"monthly_cost\": 45,
    \"active_users_count\": 56,
    \"owner_department\": \"Design\",
    \"status\": \"trial\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tools"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "vmqeopfuudtdsufvyvddq",
    "description": "Dolores dolorum amet iste laborum eius est dolor.",
    "vendor": "dtdsufvyvddqamniihfqc",
    "website_url": "http:\/\/www.weimann.com\/perferendis-voluptatibus-incidunt-nostrum-quia-possimus.html",
    "category_id": 17,
    "monthly_cost": 45,
    "active_users_count": 56,
    "owner_department": "Design",
    "status": "trial"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-tools">
</span>
<span id="execution-results-POSTapi-tools" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-tools"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-tools"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-tools" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-tools">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-tools" data-method="POST"
      data-path="api/tools"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-tools', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-tools"
                    onclick="tryItOut('POSTapi-tools');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-tools"
                    onclick="cancelTryOut('POSTapi-tools');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-tools"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/tools</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-tools"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-tools"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-tools"
               value="vmqeopfuudtdsufvyvddq"
               data-component="body">
    <br>
<p>Must be at least 2 characters. Must not be greater than 100 characters. Example: <code>vmqeopfuudtdsufvyvddq</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="POSTapi-tools"
               value="Dolores dolorum amet iste laborum eius est dolor."
               data-component="body">
    <br>
<p>Example: <code>Dolores dolorum amet iste laborum eius est dolor.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>vendor</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="vendor"                data-endpoint="POSTapi-tools"
               value="dtdsufvyvddqamniihfqc"
               data-component="body">
    <br>
<p>Must not be greater than 100 characters. Example: <code>dtdsufvyvddqamniihfqc</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>website_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="website_url"                data-endpoint="POSTapi-tools"
               value="http://www.weimann.com/perferendis-voluptatibus-incidunt-nostrum-quia-possimus.html"
               data-component="body">
    <br>
<p>Must be a valid URL. Must not be greater than 255 characters. Example: <code>http://www.weimann.com/perferendis-voluptatibus-incidunt-nostrum-quia-possimus.html</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>category_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="category_id"                data-endpoint="POSTapi-tools"
               value="17"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the categories table. Example: <code>17</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>monthly_cost</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="monthly_cost"                data-endpoint="POSTapi-tools"
               value="45"
               data-component="body">
    <br>
<p>Must be at least 0. Example: <code>45</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>active_users_count</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="active_users_count"                data-endpoint="POSTapi-tools"
               value="56"
               data-component="body">
    <br>
<p>Must be at least 0. Example: <code>56</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>owner_department</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="owner_department"                data-endpoint="POSTapi-tools"
               value="Design"
               data-component="body">
    <br>
<p>Example: <code>Design</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Engineering</code></li> <li><code>Sales</code></li> <li><code>Marketing</code></li> <li><code>HR</code></li> <li><code>Finance</code></li> <li><code>Operations</code></li> <li><code>Design</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="POSTapi-tools"
               value="trial"
               data-component="body">
    <br>
<p>Example: <code>trial</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>active</code></li> <li><code>deprecated</code></li> <li><code>trial</code></li></ul>
        </div>
        </form>

                    <h2 id="endpoints-GETapi-tools--id-">Display the specified tool with full details.</h2>

<p>
</p>



<span id="example-requests-GETapi-tools--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/tools/2" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tools/2"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-tools--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 2,
    &quot;name&quot;: &quot;Zoom&quot;,
    &quot;description&quot;: &quot;Video conferencing and webinars&quot;,
    &quot;vendor&quot;: &quot;Zoom Video Communications&quot;,
    &quot;website_url&quot;: &quot;https://zoom.us&quot;,
    &quot;category&quot;: &quot;Communication&quot;,
    &quot;monthly_cost&quot;: 14.99,
    &quot;owner_department&quot;: &quot;Operations&quot;,
    &quot;status&quot;: &quot;active&quot;,
    &quot;active_users_count&quot;: 25,
    &quot;total_monthly_cost&quot;: 374.75,
    &quot;created_at&quot;: &quot;2025-11-27T11:14:00.756108Z&quot;,
    &quot;updated_at&quot;: &quot;2025-11-27T11:14:00.756108Z&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-tools--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-tools--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-tools--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-tools--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-tools--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-tools--id-" data-method="GET"
      data-path="api/tools/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-tools--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-tools--id-"
                    onclick="tryItOut('GETapi-tools--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-tools--id-"
                    onclick="cancelTryOut('GETapi-tools--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-tools--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/tools/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-tools--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-tools--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-tools--id-"
               value="2"
               data-component="url">
    <br>
<p>The ID of the tool. Example: <code>2</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-PUTapi-tools--id-">Update the specified tool in storage.</h2>

<p>
</p>



<span id="example-requests-PUTapi-tools--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost:8000/api/tools/2" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"vmqeopfuudtdsufvyvddq\",
    \"description\": \"Dolores dolorum amet iste laborum eius est dolor.\",
    \"vendor\": \"dtdsufvyvddqamniihfqc\",
    \"website_url\": \"http:\\/\\/www.weimann.com\\/perferendis-voluptatibus-incidunt-nostrum-quia-possimus.html\",
    \"category_id\": 17,
    \"monthly_cost\": 45,
    \"active_users_count\": 56,
    \"owner_department\": \"Marketing\",
    \"status\": \"active\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tools/2"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "vmqeopfuudtdsufvyvddq",
    "description": "Dolores dolorum amet iste laborum eius est dolor.",
    "vendor": "dtdsufvyvddqamniihfqc",
    "website_url": "http:\/\/www.weimann.com\/perferendis-voluptatibus-incidunt-nostrum-quia-possimus.html",
    "category_id": 17,
    "monthly_cost": 45,
    "active_users_count": 56,
    "owner_department": "Marketing",
    "status": "active"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-tools--id-">
</span>
<span id="execution-results-PUTapi-tools--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-tools--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-tools--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-tools--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-tools--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-tools--id-" data-method="PUT"
      data-path="api/tools/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-tools--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-tools--id-"
                    onclick="tryItOut('PUTapi-tools--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-tools--id-"
                    onclick="cancelTryOut('PUTapi-tools--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-tools--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/tools/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/tools/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-tools--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-tools--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-tools--id-"
               value="2"
               data-component="url">
    <br>
<p>The ID of the tool. Example: <code>2</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-tools--id-"
               value="vmqeopfuudtdsufvyvddq"
               data-component="body">
    <br>
<p>Must be at least 2 characters. Must not be greater than 100 characters. Example: <code>vmqeopfuudtdsufvyvddq</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="PUTapi-tools--id-"
               value="Dolores dolorum amet iste laborum eius est dolor."
               data-component="body">
    <br>
<p>Example: <code>Dolores dolorum amet iste laborum eius est dolor.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>vendor</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="vendor"                data-endpoint="PUTapi-tools--id-"
               value="dtdsufvyvddqamniihfqc"
               data-component="body">
    <br>
<p>Must not be greater than 100 characters. Example: <code>dtdsufvyvddqamniihfqc</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>website_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="website_url"                data-endpoint="PUTapi-tools--id-"
               value="http://www.weimann.com/perferendis-voluptatibus-incidunt-nostrum-quia-possimus.html"
               data-component="body">
    <br>
<p>Must be a valid URL. Must not be greater than 255 characters. Example: <code>http://www.weimann.com/perferendis-voluptatibus-incidunt-nostrum-quia-possimus.html</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>category_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="category_id"                data-endpoint="PUTapi-tools--id-"
               value="17"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the categories table. Example: <code>17</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>monthly_cost</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="monthly_cost"                data-endpoint="PUTapi-tools--id-"
               value="45"
               data-component="body">
    <br>
<p>Must be at least 0. Example: <code>45</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>active_users_count</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="active_users_count"                data-endpoint="PUTapi-tools--id-"
               value="56"
               data-component="body">
    <br>
<p>Must be at least 0. Example: <code>56</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>owner_department</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="owner_department"                data-endpoint="PUTapi-tools--id-"
               value="Marketing"
               data-component="body">
    <br>
<p>Example: <code>Marketing</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Engineering</code></li> <li><code>Sales</code></li> <li><code>Marketing</code></li> <li><code>HR</code></li> <li><code>Finance</code></li> <li><code>Operations</code></li> <li><code>Design</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="PUTapi-tools--id-"
               value="active"
               data-component="body">
    <br>
<p>Example: <code>active</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>active</code></li> <li><code>deprecated</code></li> <li><code>trial</code></li></ul>
        </div>
        </form>

                    <h2 id="endpoints-DELETEapi-tools--id-">Remove the specified tool from storage.</h2>

<p>
</p>



<span id="example-requests-DELETEapi-tools--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost:8000/api/tools/2" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tools/2"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-tools--id-">
</span>
<span id="execution-results-DELETEapi-tools--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-tools--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-tools--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-tools--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-tools--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-tools--id-" data-method="DELETE"
      data-path="api/tools/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-tools--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-tools--id-"
                    onclick="tryItOut('DELETEapi-tools--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-tools--id-"
                    onclick="cancelTryOut('DELETEapi-tools--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-tools--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/tools/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-tools--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-tools--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-tools--id-"
               value="2"
               data-component="url">
    <br>
<p>The ID of the tool. Example: <code>2</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-analytics-department-costs">Get cost breakdown by department</h2>

<p>
</p>



<span id="example-requests-GETapi-analytics-department-costs">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/analytics/department-costs" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/analytics/department-costs"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-analytics-department-costs">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;total_cost&quot;: 1573.47,
            &quot;tools_count&quot;: 60,
            &quot;total_users&quot;: 2194,
            &quot;average_cost_per_tool&quot;: 26.22,
            &quot;cost_percentage&quot;: 55.8
        },
        {
            &quot;department&quot;: &quot;Design&quot;,
            &quot;total_cost&quot;: 717,
            &quot;tools_count&quot;: 57,
            &quot;total_users&quot;: 829,
            &quot;average_cost_per_tool&quot;: 12.58,
            &quot;cost_percentage&quot;: 25.4
        },
        {
            &quot;department&quot;: &quot;Marketing&quot;,
            &quot;total_cost&quot;: 227.98,
            &quot;tools_count&quot;: 5,
            &quot;total_users&quot;: 30,
            &quot;average_cost_per_tool&quot;: 45.6,
            &quot;cost_percentage&quot;: 8.1
        },
        {
            &quot;department&quot;: &quot;Operations&quot;,
            &quot;total_cost&quot;: 124.97,
            &quot;tools_count&quot;: 6,
            &quot;total_users&quot;: 127,
            &quot;average_cost_per_tool&quot;: 20.83,
            &quot;cost_percentage&quot;: 4.4
        },
        {
            &quot;department&quot;: &quot;Finance&quot;,
            &quot;total_cost&quot;: 95,
            &quot;tools_count&quot;: 2,
            &quot;total_users&quot;: 6,
            &quot;average_cost_per_tool&quot;: 47.5,
            &quot;cost_percentage&quot;: 3.4
        },
        {
            &quot;department&quot;: &quot;Sales&quot;,
            &quot;total_cost&quot;: 75,
            &quot;tools_count&quot;: 1,
            &quot;total_users&quot;: 1,
            &quot;average_cost_per_tool&quot;: 75,
            &quot;cost_percentage&quot;: 2.7
        },
        {
            &quot;department&quot;: &quot;HR&quot;,
            &quot;total_cost&quot;: 6.19,
            &quot;tools_count&quot;: 1,
            &quot;total_users&quot;: 3,
            &quot;average_cost_per_tool&quot;: 6.19,
            &quot;cost_percentage&quot;: 0.2
        }
    ],
    &quot;summary&quot;: {
        &quot;total_company_cost&quot;: 2819.61,
        &quot;departments_count&quot;: 7,
        &quot;most_expensive_department&quot;: &quot;Engineering&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-analytics-department-costs" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-analytics-department-costs"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-analytics-department-costs"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-analytics-department-costs" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-analytics-department-costs">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-analytics-department-costs" data-method="GET"
      data-path="api/analytics/department-costs"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-analytics-department-costs', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-analytics-department-costs"
                    onclick="tryItOut('GETapi-analytics-department-costs');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-analytics-department-costs"
                    onclick="cancelTryOut('GETapi-analytics-department-costs');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-analytics-department-costs"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/analytics/department-costs</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-analytics-department-costs"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-analytics-department-costs"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-analytics-expensive-tools">Get top expensive tools with efficiency analysis</h2>

<p>
</p>



<span id="example-requests-GETapi-analytics-expensive-tools">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/analytics/expensive-tools" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/analytics/expensive-tools"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-analytics-expensive-tools">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 21,
            &quot;name&quot;: &quot;Specialized Analytics Pro&quot;,
            &quot;monthly_cost&quot;: 159.99,
            &quot;active_users_count&quot;: 1,
            &quot;cost_per_user&quot;: 159.99,
            &quot;department&quot;: &quot;Marketing&quot;,
            &quot;vendor&quot;: &quot;DataCorp&quot;,
            &quot;efficiency_rating&quot;: &quot;low&quot;
        },
        {
            &quot;id&quot;: 20,
            &quot;name&quot;: &quot;AWS&quot;,
            &quot;monthly_cost&quot;: 150,
            &quot;active_users_count&quot;: 2,
            &quot;cost_per_user&quot;: 75,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Amazon Web Services&quot;,
            &quot;efficiency_rating&quot;: &quot;low&quot;
        },
        {
            &quot;id&quot;: 25,
            &quot;name&quot;: &quot;New Test Tool&quot;,
            &quot;monthly_cost&quot;: 99.99,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 99.99,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;efficiency_rating&quot;: &quot;low&quot;
        },
        {
            &quot;id&quot;: 26,
            &quot;name&quot;: &quot;New Test Tool&quot;,
            &quot;monthly_cost&quot;: 99.99,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 99.99,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;efficiency_rating&quot;: &quot;low&quot;
        },
        {
            &quot;id&quot;: 27,
            &quot;name&quot;: &quot;New Test Tool&quot;,
            &quot;monthly_cost&quot;: 99.99,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 99.99,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;efficiency_rating&quot;: &quot;low&quot;
        },
        {
            &quot;id&quot;: 22,
            &quot;name&quot;: &quot;Enterprise Security Suite&quot;,
            &quot;monthly_cost&quot;: 89.99,
            &quot;active_users_count&quot;: 2,
            &quot;cost_per_user&quot;: 45,
            &quot;department&quot;: &quot;Operations&quot;,
            &quot;vendor&quot;: &quot;SecureCorp&quot;,
            &quot;efficiency_rating&quot;: &quot;low&quot;
        },
        {
            &quot;id&quot;: 24,
            &quot;name&quot;: &quot;Legacy CRM System&quot;,
            &quot;monthly_cost&quot;: 75,
            &quot;active_users_count&quot;: 1,
            &quot;cost_per_user&quot;: 75,
            &quot;department&quot;: &quot;Sales&quot;,
            &quot;vendor&quot;: &quot;OldTech&quot;,
            &quot;efficiency_rating&quot;: &quot;low&quot;
        },
        {
            &quot;id&quot;: 13,
            &quot;name&quot;: &quot;Tableau&quot;,
            &quot;monthly_cost&quot;: 70,
            &quot;active_users_count&quot;: 3,
            &quot;cost_per_user&quot;: 23.33,
            &quot;department&quot;: &quot;Finance&quot;,
            &quot;vendor&quot;: &quot;Salesforce&quot;,
            &quot;efficiency_rating&quot;: &quot;low&quot;
        },
        {
            &quot;id&quot;: 16,
            &quot;name&quot;: &quot;HubSpot&quot;,
            &quot;monthly_cost&quot;: 45,
            &quot;active_users_count&quot;: 6,
            &quot;cost_per_user&quot;: 7.5,
            &quot;department&quot;: &quot;Marketing&quot;,
            &quot;vendor&quot;: &quot;HubSpot Inc.&quot;,
            &quot;efficiency_rating&quot;: &quot;low&quot;
        },
        {
            &quot;id&quot;: 23,
            &quot;name&quot;: &quot;Premium Design Tools&quot;,
            &quot;monthly_cost&quot;: 45,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 45,
            &quot;department&quot;: &quot;Design&quot;,
            &quot;vendor&quot;: &quot;DesignPro&quot;,
            &quot;efficiency_rating&quot;: &quot;low&quot;
        }
    ],
    &quot;analysis&quot;: {
        &quot;total_tools_analyzed&quot;: 132,
        &quot;avg_cost_per_user_company&quot;: 0.74,
        &quot;potential_savings_identified&quot;: 934.95
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-analytics-expensive-tools" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-analytics-expensive-tools"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-analytics-expensive-tools"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-analytics-expensive-tools" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-analytics-expensive-tools">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-analytics-expensive-tools" data-method="GET"
      data-path="api/analytics/expensive-tools"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-analytics-expensive-tools', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-analytics-expensive-tools"
                    onclick="tryItOut('GETapi-analytics-expensive-tools');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-analytics-expensive-tools"
                    onclick="cancelTryOut('GETapi-analytics-expensive-tools');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-analytics-expensive-tools"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/analytics/expensive-tools</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-analytics-expensive-tools"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-analytics-expensive-tools"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-analytics-tools-by-category">Get tools distribution by category</h2>

<p>
</p>



<span id="example-requests-GETapi-analytics-tools-by-category">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/analytics/tools-by-category" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/analytics/tools-by-category"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-analytics-tools-by-category">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;category_name&quot;: &quot;Analytics&quot;,
            &quot;tools_count&quot;: 3,
            &quot;total_cost&quot;: 229.99,
            &quot;total_users&quot;: 16,
            &quot;percentage_of_budget&quot;: 8.2,
            &quot;average_cost_per_user&quot;: 14.37
        },
        {
            &quot;category_name&quot;: &quot;Infrastructure&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 150,
            &quot;total_users&quot;: 2,
            &quot;percentage_of_budget&quot;: 5.3,
            &quot;average_cost_per_user&quot;: 75
        },
        {
            &quot;category_name&quot;: &quot;Communication&quot;,
            &quot;tools_count&quot;: 9,
            &quot;total_cost&quot;: 130.49,
            &quot;total_users&quot;: 25,
            &quot;percentage_of_budget&quot;: 4.6,
            &quot;average_cost_per_user&quot;: 5.22
        },
        {
            &quot;category_name&quot;: &quot;Marketing&quot;,
            &quot;tools_count&quot;: 3,
            &quot;total_cost&quot;: 130,
            &quot;total_users&quot;: 10,
            &quot;percentage_of_budget&quot;: 4.6,
            &quot;average_cost_per_user&quot;: 13
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 63043&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 99.99,
            &quot;total_users&quot;: 0,
            &quot;percentage_of_budget&quot;: 3.5,
            &quot;average_cost_per_user&quot;: 0
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 43486&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 99.99,
            &quot;total_users&quot;: 0,
            &quot;percentage_of_budget&quot;: 3.5,
            &quot;average_cost_per_user&quot;: 0
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 43382&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 99.99,
            &quot;total_users&quot;: 0,
            &quot;percentage_of_budget&quot;: 3.5,
            &quot;average_cost_per_user&quot;: 0
        },
        {
            &quot;category_name&quot;: &quot;Security&quot;,
            &quot;tools_count&quot;: 3,
            &quot;total_cost&quot;: 95.98,
            &quot;total_users&quot;: 52,
            &quot;percentage_of_budget&quot;: 3.4,
            &quot;average_cost_per_user&quot;: 1.85
        },
        {
            &quot;category_name&quot;: &quot;Design&quot;,
            &quot;tools_count&quot;: 3,
            &quot;total_cost&quot;: 69.99,
            &quot;total_users&quot;: 12,
            &quot;percentage_of_budget&quot;: 2.5,
            &quot;average_cost_per_user&quot;: 5.83
        },
        {
            &quot;category_name&quot;: &quot;Development&quot;,
            &quot;tools_count&quot;: 5,
            &quot;total_cost&quot;: 51,
            &quot;total_users&quot;: 42,
            &quot;percentage_of_budget&quot;: 1.8,
            &quot;average_cost_per_user&quot;: 1.21
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 10855&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 30,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 1.1,
            &quot;average_cost_per_user&quot;: 0.6
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 11811&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 30,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 1.1,
            &quot;average_cost_per_user&quot;: 0.6
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 32260&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 30,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 1.1,
            &quot;average_cost_per_user&quot;: 0.6
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 41106&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 30,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 1.1,
            &quot;average_cost_per_user&quot;: 0.6
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 35425&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 30,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 1.1,
            &quot;average_cost_per_user&quot;: 0.6
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 10110&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 30,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 1.1,
            &quot;average_cost_per_user&quot;: 0.6
        },
        {
            &quot;category_name&quot;: &quot;Finance&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 25,
            &quot;total_users&quot;: 3,
            &quot;percentage_of_budget&quot;: 0.9,
            &quot;average_cost_per_user&quot;: 8.33
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 89432&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 61757&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 16835&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 89993&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 76374&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 57597&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 60410&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 26841&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 83615&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 87346&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 79820&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 22531&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 64532&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 10308&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 90771&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 48250&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 84083&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 85773&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 76581&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 14801&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 79969&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 14561&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 19460&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 84100&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 36896&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 95176&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 57191&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 12095&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 25542&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 95166&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 90892&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 55876&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 28765&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 66981&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 56395&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 62328&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Test Development Tools 65955&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 21,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.7,
            &quot;average_cost_per_user&quot;: 0.42
        },
        {
            &quot;category_name&quot;: &quot;Productivity&quot;,
            &quot;tools_count&quot;: 2,
            &quot;total_cost&quot;: 14,
            &quot;total_users&quot;: 50,
            &quot;percentage_of_budget&quot;: 0.5,
            &quot;average_cost_per_user&quot;: 0.28
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 55876&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 76581&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 84083&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 95166&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 89432&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 95176&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 57191&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 26841&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 35425&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 70236&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 87346&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 89993&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 41941&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 12095&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 83615&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 81819&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 60410&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 14561&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 65955&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 16835&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 90771&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 28765&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 84381&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 38302&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 84100&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 10308&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 24721&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 76374&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 79820&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 25542&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 24908&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 35245&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 41106&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 22531&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 95353&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 57597&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 62328&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 14801&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 61757&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 56395&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 90892&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 85773&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 79969&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 37104&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 10110&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 36896&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 19460&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 64532&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 32260&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 11811&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 10855&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 66981&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 75867&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 35645&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;Test Design Tools 48250&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 12,
            &quot;total_users&quot;: 15,
            &quot;percentage_of_budget&quot;: 0.4,
            &quot;average_cost_per_user&quot;: 0.8
        },
        {
            &quot;category_name&quot;: &quot;HR&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_cost&quot;: 6.19,
            &quot;total_users&quot;: 3,
            &quot;percentage_of_budget&quot;: 0.2,
            &quot;average_cost_per_user&quot;: 2.06
        }
    ],
    &quot;insights&quot;: {
        &quot;most_expensive_category&quot;: &quot;Analytics&quot;,
        &quot;most_efficient_category&quot;: &quot;Productivity&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-analytics-tools-by-category" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-analytics-tools-by-category"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-analytics-tools-by-category"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-analytics-tools-by-category" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-analytics-tools-by-category">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-analytics-tools-by-category" data-method="GET"
      data-path="api/analytics/tools-by-category"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-analytics-tools-by-category', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-analytics-tools-by-category"
                    onclick="tryItOut('GETapi-analytics-tools-by-category');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-analytics-tools-by-category"
                    onclick="cancelTryOut('GETapi-analytics-tools-by-category');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-analytics-tools-by-category"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/analytics/tools-by-category</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-analytics-tools-by-category"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-analytics-tools-by-category"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-analytics-low-usage-tools">Get low usage tools with potential savings</h2>

<p>
</p>



<span id="example-requests-GETapi-analytics-low-usage-tools">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/analytics/low-usage-tools" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/analytics/low-usage-tools"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-analytics-low-usage-tools">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 21,
            &quot;name&quot;: &quot;Specialized Analytics Pro&quot;,
            &quot;monthly_cost&quot;: 159.99,
            &quot;active_users_count&quot;: 1,
            &quot;cost_per_user&quot;: 159.99,
            &quot;department&quot;: &quot;Marketing&quot;,
            &quot;vendor&quot;: &quot;DataCorp&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 20,
            &quot;name&quot;: &quot;AWS&quot;,
            &quot;monthly_cost&quot;: 150,
            &quot;active_users_count&quot;: 2,
            &quot;cost_per_user&quot;: 75,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Amazon Web Services&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 25,
            &quot;name&quot;: &quot;New Test Tool&quot;,
            &quot;monthly_cost&quot;: 99.99,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 99.99,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 26,
            &quot;name&quot;: &quot;New Test Tool&quot;,
            &quot;monthly_cost&quot;: 99.99,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 99.99,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 27,
            &quot;name&quot;: &quot;New Test Tool&quot;,
            &quot;monthly_cost&quot;: 99.99,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 99.99,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 22,
            &quot;name&quot;: &quot;Enterprise Security Suite&quot;,
            &quot;monthly_cost&quot;: 89.99,
            &quot;active_users_count&quot;: 2,
            &quot;cost_per_user&quot;: 45,
            &quot;department&quot;: &quot;Operations&quot;,
            &quot;vendor&quot;: &quot;SecureCorp&quot;,
            &quot;warning_level&quot;: &quot;medium&quot;,
            &quot;potential_action&quot;: &quot;Review usage and consider optimization&quot;
        },
        {
            &quot;id&quot;: 24,
            &quot;name&quot;: &quot;Legacy CRM System&quot;,
            &quot;monthly_cost&quot;: 75,
            &quot;active_users_count&quot;: 1,
            &quot;cost_per_user&quot;: 75,
            &quot;department&quot;: &quot;Sales&quot;,
            &quot;vendor&quot;: &quot;OldTech&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 13,
            &quot;name&quot;: &quot;Tableau&quot;,
            &quot;monthly_cost&quot;: 70,
            &quot;active_users_count&quot;: 3,
            &quot;cost_per_user&quot;: 23.33,
            &quot;department&quot;: &quot;Finance&quot;,
            &quot;vendor&quot;: &quot;Salesforce&quot;,
            &quot;warning_level&quot;: &quot;medium&quot;,
            &quot;potential_action&quot;: &quot;Review usage and consider optimization&quot;
        },
        {
            &quot;id&quot;: 23,
            &quot;name&quot;: &quot;Premium Design Tools&quot;,
            &quot;monthly_cost&quot;: 45,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 45,
            &quot;department&quot;: &quot;Design&quot;,
            &quot;vendor&quot;: &quot;DesignPro&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 19,
            &quot;name&quot;: &quot;QuickBooks&quot;,
            &quot;monthly_cost&quot;: 25,
            &quot;active_users_count&quot;: 3,
            &quot;cost_per_user&quot;: 8.33,
            &quot;department&quot;: &quot;Finance&quot;,
            &quot;vendor&quot;: &quot;Intuit Inc.&quot;,
            &quot;warning_level&quot;: &quot;low&quot;,
            &quot;potential_action&quot;: &quot;Monitor usage trends&quot;
        },
        {
            &quot;id&quot;: 48,
            &quot;name&quot;: &quot;Test Tool Delete 1764250165312&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 15,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 62,
            &quot;name&quot;: &quot;Test Tool Delete 1764251672234&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 15,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 76,
            &quot;name&quot;: &quot;Test Tool Delete 1764251909068&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 15,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 69,
            &quot;name&quot;: &quot;Test Tool Delete 1764251797614&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 15,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 34,
            &quot;name&quot;: &quot;Test Tool Delete 1764249012441&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 15,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 41,
            &quot;name&quot;: &quot;Test Tool Delete 1764249099225&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 15,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 55,
            &quot;name&quot;: &quot;Test Tool Delete 1764250175735&quot;,
            &quot;monthly_cost&quot;: 15,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 15,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 7,
            &quot;name&quot;: &quot;Postman&quot;,
            &quot;monthly_cost&quot;: 12,
            &quot;active_users_count&quot;: 5,
            &quot;cost_per_user&quot;: 2.4,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Postman Inc.&quot;,
            &quot;warning_level&quot;: &quot;low&quot;,
            &quot;potential_action&quot;: &quot;Monitor usage trends&quot;
        },
        {
            &quot;id&quot;: 8,
            &quot;name&quot;: &quot;Figma&quot;,
            &quot;monthly_cost&quot;: 12,
            &quot;active_users_count&quot;: 4,
            &quot;cost_per_user&quot;: 3,
            &quot;department&quot;: &quot;Design&quot;,
            &quot;vendor&quot;: &quot;Figma Inc.&quot;,
            &quot;warning_level&quot;: &quot;low&quot;,
            &quot;potential_action&quot;: &quot;Monitor usage trends&quot;
        },
        {
            &quot;id&quot;: 78,
            &quot;name&quot;: &quot;Test Tool&quot;,
            &quot;monthly_cost&quot;: 10.5,
            &quot;active_users_count&quot;: 0,
            &quot;cost_per_user&quot;: 10.5,
            &quot;department&quot;: &quot;Engineering&quot;,
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;warning_level&quot;: &quot;high&quot;,
            &quot;potential_action&quot;: &quot;Consider canceling or downgrading&quot;
        },
        {
            &quot;id&quot;: 17,
            &quot;name&quot;: &quot;Mailchimp&quot;,
            &quot;monthly_cost&quot;: 10,
            &quot;active_users_count&quot;: 3,
            &quot;cost_per_user&quot;: 3.33,
            &quot;department&quot;: &quot;Marketing&quot;,
            &quot;vendor&quot;: &quot;Intuit Mailchimp&quot;,
            &quot;warning_level&quot;: &quot;low&quot;,
            &quot;potential_action&quot;: &quot;Monitor usage trends&quot;
        },
        {
            &quot;id&quot;: 18,
            &quot;name&quot;: &quot;BambooHR&quot;,
            &quot;monthly_cost&quot;: 6.19,
            &quot;active_users_count&quot;: 3,
            &quot;cost_per_user&quot;: 2.06,
            &quot;department&quot;: &quot;HR&quot;,
            &quot;vendor&quot;: &quot;BambooHR LLC&quot;,
            &quot;warning_level&quot;: &quot;low&quot;,
            &quot;potential_action&quot;: &quot;Monitor usage trends&quot;
        }
    ],
    &quot;savings_analysis&quot;: {
        &quot;total_underutilized_tools&quot;: 22,
        &quot;potential_monthly_savings&quot;: 1005.45,
        &quot;potential_annual_savings&quot;: 12065.400000000001
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-analytics-low-usage-tools" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-analytics-low-usage-tools"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-analytics-low-usage-tools"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-analytics-low-usage-tools" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-analytics-low-usage-tools">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-analytics-low-usage-tools" data-method="GET"
      data-path="api/analytics/low-usage-tools"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-analytics-low-usage-tools', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-analytics-low-usage-tools"
                    onclick="tryItOut('GETapi-analytics-low-usage-tools');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-analytics-low-usage-tools"
                    onclick="cancelTryOut('GETapi-analytics-low-usage-tools');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-analytics-low-usage-tools"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/analytics/low-usage-tools</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-analytics-low-usage-tools"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-analytics-low-usage-tools"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-analytics-vendor-summary">Get vendor summary and analysis</h2>

<p>
</p>



<span id="example-requests-GETapi-analytics-vendor-summary">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/analytics/vendor-summary" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/analytics/vendor-summary"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-analytics-vendor-summary">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;vendor&quot;: &quot;GitHub&quot;,
            &quot;tools_count&quot;: 43,
            &quot;total_monthly_cost&quot;: 957,
            &quot;total_users&quot;: 2150,
            &quot;departments&quot;: &quot;Engineering&quot;,
            &quot;average_cost_per_user&quot;: 0.45,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Figma&quot;,
            &quot;tools_count&quot;: 55,
            &quot;total_monthly_cost&quot;: 660,
            &quot;total_users&quot;: 825,
            &quot;departments&quot;: &quot;Design&quot;,
            &quot;average_cost_per_user&quot;: 0.8,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Test Vendor&quot;,
            &quot;tools_count&quot;: 11,
            &quot;total_monthly_cost&quot;: 415.47,
            &quot;total_users&quot;: 0,
            &quot;departments&quot;: &quot;Engineering&quot;,
            &quot;average_cost_per_user&quot;: 0,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;DataCorp&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 159.99,
            &quot;total_users&quot;: 1,
            &quot;departments&quot;: &quot;Marketing&quot;,
            &quot;average_cost_per_user&quot;: 159.99,
            &quot;vendor_efficiency&quot;: &quot;poor&quot;
        },
        {
            &quot;vendor&quot;: &quot;Amazon Web Services&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 150,
            &quot;total_users&quot;: 2,
            &quot;departments&quot;: &quot;Engineering&quot;,
            &quot;average_cost_per_user&quot;: 75,
            &quot;vendor_efficiency&quot;: &quot;poor&quot;
        },
        {
            &quot;vendor&quot;: &quot;SecureCorp&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 89.99,
            &quot;total_users&quot;: 2,
            &quot;departments&quot;: &quot;Operations&quot;,
            &quot;average_cost_per_user&quot;: 45,
            &quot;vendor_efficiency&quot;: &quot;poor&quot;
        },
        {
            &quot;vendor&quot;: &quot;OldTech&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 75,
            &quot;total_users&quot;: 1,
            &quot;departments&quot;: &quot;Sales&quot;,
            &quot;average_cost_per_user&quot;: 75,
            &quot;vendor_efficiency&quot;: &quot;poor&quot;
        },
        {
            &quot;vendor&quot;: &quot;Salesforce&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 70,
            &quot;total_users&quot;: 3,
            &quot;departments&quot;: &quot;Finance&quot;,
            &quot;average_cost_per_user&quot;: 23.33,
            &quot;vendor_efficiency&quot;: &quot;average&quot;
        },
        {
            &quot;vendor&quot;: &quot;HubSpot Inc.&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 45,
            &quot;total_users&quot;: 6,
            &quot;departments&quot;: &quot;Marketing&quot;,
            &quot;average_cost_per_user&quot;: 7.5,
            &quot;vendor_efficiency&quot;: &quot;good&quot;
        },
        {
            &quot;vendor&quot;: &quot;DesignPro&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 45,
            &quot;total_users&quot;: 0,
            &quot;departments&quot;: &quot;Design&quot;,
            &quot;average_cost_per_user&quot;: 0,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Intuit Inc.&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 25,
            &quot;total_users&quot;: 3,
            &quot;departments&quot;: &quot;Finance&quot;,
            &quot;average_cost_per_user&quot;: 8.33,
            &quot;vendor_efficiency&quot;: &quot;good&quot;
        },
        {
            &quot;vendor&quot;: &quot;GitHub Inc.&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 21,
            &quot;total_users&quot;: 10,
            &quot;departments&quot;: &quot;Engineering&quot;,
            &quot;average_cost_per_user&quot;: 2.1,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Zoom Video Communications&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 14.99,
            &quot;total_users&quot;: 25,
            &quot;departments&quot;: &quot;Operations&quot;,
            &quot;average_cost_per_user&quot;: 0.6,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Atlassian&quot;,
            &quot;tools_count&quot;: 2,
            &quot;total_monthly_cost&quot;: 13,
            &quot;total_users&quot;: 20,
            &quot;departments&quot;: &quot;Engineering&quot;,
            &quot;average_cost_per_user&quot;: 0.65,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Canva Pty Ltd.&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 12.99,
            &quot;total_users&quot;: 8,
            &quot;departments&quot;: &quot;Marketing&quot;,
            &quot;average_cost_per_user&quot;: 1.62,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Figma Inc.&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 12,
            &quot;total_users&quot;: 4,
            &quot;departments&quot;: &quot;Design&quot;,
            &quot;average_cost_per_user&quot;: 3,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Postman Inc.&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 12,
            &quot;total_users&quot;: 5,
            &quot;departments&quot;: &quot;Engineering&quot;,
            &quot;average_cost_per_user&quot;: 2.4,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Intuit Mailchimp&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 10,
            &quot;total_users&quot;: 3,
            &quot;departments&quot;: &quot;Marketing&quot;,
            &quot;average_cost_per_user&quot;: 3.33,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Notion Labs Inc.&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 8,
            &quot;total_users&quot;: 25,
            &quot;departments&quot;: &quot;Operations&quot;,
            &quot;average_cost_per_user&quot;: 0.32,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;BambooHR LLC&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 6.19,
            &quot;total_users&quot;: 3,
            &quot;departments&quot;: &quot;HR&quot;,
            &quot;average_cost_per_user&quot;: 2.06,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Google LLC&quot;,
            &quot;tools_count&quot;: 2,
            &quot;total_monthly_cost&quot;: 6,
            &quot;total_users&quot;: 37,
            &quot;departments&quot;: &quot;Marketing,Operations&quot;,
            &quot;average_cost_per_user&quot;: 0.16,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Docker Inc.&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 5,
            &quot;total_users&quot;: 7,
            &quot;departments&quot;: &quot;Engineering&quot;,
            &quot;average_cost_per_user&quot;: 0.71,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;1Password&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 3.99,
            &quot;total_users&quot;: 25,
            &quot;departments&quot;: &quot;Operations&quot;,
            &quot;average_cost_per_user&quot;: 0.16,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        },
        {
            &quot;vendor&quot;: &quot;Okta Inc.&quot;,
            &quot;tools_count&quot;: 1,
            &quot;total_monthly_cost&quot;: 2,
            &quot;total_users&quot;: 25,
            &quot;departments&quot;: &quot;Operations&quot;,
            &quot;average_cost_per_user&quot;: 0.08,
            &quot;vendor_efficiency&quot;: &quot;excellent&quot;
        }
    ],
    &quot;vendor_insights&quot;: {
        &quot;most_expensive_vendor&quot;: &quot;GitHub&quot;,
        &quot;most_efficient_vendor&quot;: &quot;Okta Inc.&quot;,
        &quot;single_tool_vendors&quot;: 19
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-analytics-vendor-summary" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-analytics-vendor-summary"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-analytics-vendor-summary"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-analytics-vendor-summary" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-analytics-vendor-summary">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-analytics-vendor-summary" data-method="GET"
      data-path="api/analytics/vendor-summary"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-analytics-vendor-summary', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-analytics-vendor-summary"
                    onclick="tryItOut('GETapi-analytics-vendor-summary');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-analytics-vendor-summary"
                    onclick="cancelTryOut('GETapi-analytics-vendor-summary');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-analytics-vendor-summary"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/analytics/vendor-summary</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-analytics-vendor-summary"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-analytics-vendor-summary"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
