<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- convert "document": create header and continue -->
<xsl:template match="document">
  <TeXML>
    <!-- create header -->
    <TeXML escape="0">
\documentclass{article}
\usepackage[T2A]{fontenc}
\usepackage[koi8-r]{inputenc}
\usepackage[unicode]{hyperref}
    </TeXML>
    <!-- process content -->
    <env name="document">
      <xsl:apply-templates/>
    </env>
  </TeXML>
</xsl:template>

<!-- convert "para": process content and add "\par" -->
<xsl:template match="para">
  <xsl:apply-templates />
  <cmd name="par" gr="0" nl2="1" />
</xsl:template>

<!-- convert sections by converting "title" -->
<xsl:template match="title">
  <cmd name="section">
    <opt>
      <cmd name="texorpdfstring">
        <parm><xsl:value-of select="."/></parm>
        <parm><pdf><xsl:value-of select="."/></pdf></parm>
      </cmd>
    </opt>
    <parm><xsl:value-of select="."/></parm>
  </cmd>
</xsl:template>

</xsl:stylesheet>
