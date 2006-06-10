<x:stylesheet version="1.0" xmlns:x="http://www.w3.org/1999/XSL/Transform">
<!-- creates HTML from a pre-HTML -->
<x:output method="xml"/>

<x:param name="doc.id"/>

<x:template match="node()|@*">
  <x:copy>
    <x:apply-templates select="node()|@*" />
  </x:copy>
</x:template>

<x:template match="docs">
  <x:choose>
    <x:when test="not(doc[@id=$doc.id])">
      <x:message terminate="yes">Can't find document with id '<x:value-of select="$doc.id"/>'</x:message>
    </x:when>
    <x:otherwise>
      <x:apply-templates select="doc[@id=$doc.id]"/>
    </x:otherwise>
  </x:choose>
</x:template>

<x:template match="doc">
  <html id="{@id}">
    <head>
      <title><x:value-of select="title" /></title>
      <meta name="keywords" content="{keywords}" />
      <meta name="description" content="{description}" />
      <link rel="stylesheet" type="text/css" href="texml.css" />
    </head>
    <body>
      <a href="index.html"><img src="texml.png" width="100" heaight="60" alt="TeXML" title="TeXML" border="0" align="right" /></a>
      <x:if test="preceding-sibling::doc">
        <div style="width:150;float:right;font-size:66%;">
          <x:call-template name="tour-step-link">
            <x:with-param name="preword">Previous </x:with-param>
            <x:with-param name="id" select="preceding-sibling::doc[1]/@id"/>
            <x:with-param name="title" select="preceding-sibling::doc[1]/title"/>
          </x:call-template>
        </div>
      </x:if>
      <x:apply-templates select="node()" />
      <x:if  test="following-sibling::doc">
        <p>
          <x:call-template name="tour-step-link">
            <x:with-param name="preword">Next </x:with-param>
            <x:with-param name="id" select="following-sibling::doc[1]/@id"/>
            <x:with-param name="title" select="following-sibling::doc[1]/title"/>
          </x:call-template>
        </p>
      </x:if>
      <hr />
      <div class="footnote">
        <x:if test="@id!='texml.index'">
          <a href="index.html"><img src="texml.png" width="100" heaight="60" alt="TeXML" title="TeXML" border="0" vspace="10" /></a><br />
        </x:if>
        <x:variable name="url" select="concat('http://getfo.org/texml/',substring-after(@id,'texml.'),'.html')"/>
        <x:text>This page: </x:text><a href="{$url}"><x:value-of select="$url"/></a><br />
        <x:if test="@id='texml.index'">
          <x:text>Project area: </x:text><a href="http://sourceforge.net/projects/getfo/">http://sourceforge.net/projects/getfo/</a>
        </x:if>
      </div>
    </body>
  </html>
</x:template>

<x:template match="keywords | description" />

<x:template match="sect">
  <x:apply-templates select="node()|@*" />
</x:template>

<x:template match="title">
  <a name="{generate-id()}"/>
  <x:element name="{concat('h',count(ancestor::sect)+1)}">
    <x:apply-templates select="node()|@*" />
  </x:element>
</x:template>

<x:template match="example">
  <blockquote>
    <x:apply-templates select="node()|@*" />
</blockquote>
</x:template>

<x:template match="example/title">
  <para><x:apply-templates/></para>
</x:template>

<x:template match="lst">
  <pre>
    <x:apply-templates select="node()|@*" />
  </pre>
</x:template>

<x:template match="e">
  <code>
    <x:apply-templates select="node()|@*" />
  </code>
</x:template>

<x:template match="toc">
  <ul>
    <x:apply-templates select="ancestor::sect[1]/../sect" mode="toc"/>
  </ul>
</x:template>

<x:template match="sect" mode="toc">
  <x:apply-templates select="title" mode="toc"/>
</x:template>

<x:template match="title" mode="toc">
  <x:if test="not(@toc) or (1=@toc)">
    <li>
      <a href="#{generate-id()}"><x:apply-templates/></a>
      <x:if test="../sect">
        <ul>
          <x:apply-templates select="../sect" mode="toc"/>
        </ul>
      </x:if>
    </li>
  </x:if>
</x:template>

<x:template match="tour-links">
  <x:for-each select="document('tour.xml',/)/docs/doc">
    <li>Step <x:value-of select="position()"/>: <a href="{translate(substring-after(@id,'.'),'.','_')}.html"><x:value-of select="title"/></a></li>
  </x:for-each>
</x:template>

<x:template name="tour-step-link">
  <x:param name="preword"/>
  <x:param name="id"/>
  <x:param name="title"/>
  <x:value-of select="$preword"/>
  <x:text>step: </x:text>
  <x:text>&#x201c;</x:text>
  <a href="tour_{substring-after($id,'texml.tour.')}.html"><x:value-of select="translate($title,' ','&#xa0;')"/></a>
  <x:text>&#x201d;</x:text>
</x:template>

</x:stylesheet>
