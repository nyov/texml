<xsl:stylesheet 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:tei= "http://www.tei-c.org/ns/1.0"
    version="1.1"
>

    <xsl:output method="xml" encoding="ASCII"/>


    <xsl:template match="@*|node()">
        <xsl:copy> 
            <xsl:apply-templates select="@*|node()"/>
        </xsl:copy>
    </xsl:template>


    <xsl:template match="comment()">
        <xsl:comment>
            <xsl:value-of select="."/>
        </xsl:comment>
    </xsl:template>

    <xsl:template match="tei:p">
        <xsl:element name="tei:p">
            <xsl:choose>
                <xsl:when test="@rend='normal'"/>
            </xsl:choose>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tei:eg">
        <xsl:element name="tei:eg">
        <xsl:choose>
            <xsl:when test="ancestor::tei:p">
                <xsl:attribute name="rend">
                    <xsl:text>inline</xsl:text>
                </xsl:attribute>
                <xsl:attribute name="ana">
                    <xsl:text>inline-code</xsl:text>
                </xsl:attribute>
                <xsl:apply-templates/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:choose>
                    <xsl:when test="not(normalize-space(@rend))">
                        <xsl:attribute name="rend">
                            <xsl:text>context</xsl:text>
                        </xsl:attribute>
                        <xsl:attribute name="ana">
                            <xsl:text>context-block-code</xsl:text>
                        </xsl:attribute>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:attribute name="rend">
                            <xsl:value-of select="@rend"/>
                        </xsl:attribute>
                        <xsl:attribute name="ana">
                            <xsl:text>code</xsl:text>
                        </xsl:attribute>
                    </xsl:otherwise>
                </xsl:choose>
            <xsl:apply-templates/>
            </xsl:otherwise>
        </xsl:choose>
        </xsl:element>
    </xsl:template>

    <xsl:template match = "tei:div">
        <xsl:element name="tei:div">
            <xsl:for-each select = "@type">
                <xsl:choose>
                    <xsl:when test = ". = 'div1' or . = 'div2' or . = 'div3' or . = 'div4' or . = 'div5'"/>
                    <xsl:otherwise>
                        <xsl:attribute name = "type">
                            <xsl:value-of select = "."/>
                        </xsl:attribute>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>


</xsl:stylesheet>

