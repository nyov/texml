<xsl:stylesheet 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:tei= "http://www.tei-c.org/ns/1.0"
    version="1.1"
>

    <xsl:template match= "/">
        <xsl:apply-templates mode="ana"/>
    </xsl:template>

    <xsl:template match="*" mode="ana">
        <xsl:if test = "not(@ana)">
            <xsl:message>
                <xsl:text>Element </xsl:text>
                <xsl:value-of select = "name(.)"/>
                <xsl:text> does not have ana</xsl:text>
            </xsl:message>
        </xsl:if>
        <xsl:apply-templates mode="ana"/>
    </xsl:template>

    <xsl:template match="tei:TEI|tei:body|tei:text|tei:row|tei:cell|tei:note|
        tei:date|tei:xref"
        mode="ana">
        <xsl:apply-templates mode="ana"/>
    </xsl:template>

    <xsl:template match="tei:teiHeader" mode="ana"/>



    <xsl:template match="text()" mode="ana"/>
</xsl:stylesheet>
