<xsl:stylesheet 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:svg="http://www.w3.org/2000/svg" 
    version="1.1"
>
    <xsl:param name="shift" select="'false'"/>

    <xsl:output method="xml" encoding="ASCII"/>

    <xsl:template match="*">
        <xsl:variable name="name">
            <xsl:text>svg:</xsl:text>
            <xsl:value-of select="name(.)"/>
        </xsl:variable>
        <xsl:element name="{$name}">
            <xsl:for-each select="@*">
                <xsl:choose>
                    <xsl:when test = "self::y|self::y1|self::y2|self::y3|self::y4|self::y5">
                        <xsl:attribute name="{name(.)}">
                            <xsl:value-of select = ". - $shift"/>
                        </xsl:attribute>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:attribute name="{name(.)}">
                            <xsl:value-of select="."/>
                        </xsl:attribute>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>



</xsl:stylesheet>

