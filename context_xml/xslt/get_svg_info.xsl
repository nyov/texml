<xsl:stylesheet 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:tei="http://www.tei-c.org/ns/1.0"
    xmlns:svg="http://www.w3.org/2000/svg"
    version="1.1"
>

<xsl:output method="text"/>
<xsl:template match="/">
    <xsl:apply-templates select="svg|svg:svg"/>
</xsl:template>
<xsl:template match="svg|svg:svg">
    <xsl:value-of select = "@height"/>
    <xsl:text> </xsl:text>
    <xsl:value-of select="@width"/>
</xsl:template>
    
</xsl:stylesheet>
