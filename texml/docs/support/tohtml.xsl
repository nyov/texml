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
      <x:apply-templates select="node()" />
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

</x:stylesheet>
