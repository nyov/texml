<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml">
<!-- tune output for "man2html texml.1 | tidy -asxml" -->

<xsl:output method="xml" indent="yes" />

<xsl:template match="*">
  <xsl:element name="{local-name()}">
    <xsl:copy-of select="@*" />
    <xsl:apply-templates select="node()" />
  </xsl:element>
</xsl:template>

<xsl:template match="/">
  <doc id="texml.texml">
    <keywords>texml,man</keywords>
    <description>Manpage of texml</description>
    <title>Manpage of texml</title>
    <xsl:apply-templates select="/xhtml:html/xhtml:body/xhtml:h2[1]" />
    <xsl:apply-templates select="/xhtml:html/xhtml:body/xhtml:h2/following-sibling::node()[following-sibling::xhtml:hr[following-sibling::xhtml:hr]]" />
  </doc>
</xsl:template>

<!-- <xsl:template match="xhtml:body/text()['' = normalize-space()]" /> -->

<xsl:template match="xhtml:a[@name]" />

</xsl:stylesheet>
