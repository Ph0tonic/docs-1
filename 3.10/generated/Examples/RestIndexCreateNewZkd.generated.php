<span class="hljs-meta">shell&gt;</span><span class="bash"> curl -X POST --header <span class="hljs-string">&#x27;accept: application/json&#x27;</span> --data-binary @- --dump - http://localhost:8529/_api/index?collection=intervals</span> &lt;&lt;EOF
{ 
  <span class="hljs-string">&quot;type&quot;</span> : <span class="hljs-string">&quot;zkd&quot;</span>, 
  <span class="hljs-string">&quot;fields&quot;</span> : [ 
    <span class="hljs-string">&quot;from&quot;</span>, 
    <span class="hljs-string">&quot;to&quot;</span> 
  ], 
  <span class="hljs-string">&quot;fieldValueTypes&quot;</span> : <span class="hljs-string">&quot;double&quot;</span> 
}
EOF

HTTP/<span class="hljs-number">1.1</span> <span class="hljs-number">201</span> Created
content-type: application/json
connection: Keep-Alive
content-length: <span class="hljs-number">170</span>
server: ArangoDB
x-content-type-options: nosniff

{ 
  <span class="hljs-string">&quot;fields&quot;</span> : [ 
    <span class="hljs-string">&quot;from&quot;</span>, 
    <span class="hljs-string">&quot;to&quot;</span> 
  ], 
  <span class="hljs-string">&quot;id&quot;</span> : <span class="hljs-string">&quot;intervals/67777&quot;</span>, 
  <span class="hljs-string">&quot;isNewlyCreated&quot;</span> : <span class="hljs-literal">true</span>, 
  <span class="hljs-string">&quot;name&quot;</span> : <span class="hljs-string">&quot;idx_1711421383539425280&quot;</span>, 
  <span class="hljs-string">&quot;sparse&quot;</span> : <span class="hljs-literal">false</span>, 
  <span class="hljs-string">&quot;type&quot;</span> : <span class="hljs-string">&quot;zkd&quot;</span>, 
  <span class="hljs-string">&quot;unique&quot;</span> : <span class="hljs-literal">false</span>, 
  <span class="hljs-string">&quot;error&quot;</span> : <span class="hljs-literal">false</span>, 
  <span class="hljs-string">&quot;code&quot;</span> : <span class="hljs-number">201</span> 
}
