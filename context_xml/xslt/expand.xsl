<xsl:stylesheet 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    version="1.1"
>
    <xsl:import href = "/home/paul/cvs/txt2xml/xsl_stylesheets/expand_abbr.xsl"/>
    <xsl:output method="xml" encoding="ASCII"/>


    <xsl:template match="section[@sp='1']/title">
        <xsl:element name="title">
            Simple Document in <seg type="context-name" ana="context-name">context</seg> and in TeXML
        </xsl:element>
    </xsl:template>

    <xsl:template match="cm">
        <xsl:element name="seg">
            <xsl:attribute name="type">
                <xsl:text>command</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="ana">
                <xsl:text>context-command</xsl:text>
            </xsl:attribute>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="op">
        <xsl:element name="seg">
            <xsl:attribute name="type">
                <xsl:text>option</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="ana">
                <xsl:text>option</xsl:text>
            </xsl:attribute>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="ab">
        <xsl:element name="seg">
            <xsl:choose>
                <xsl:when test="normalize-space(.) = 'context'">
                    <xsl:attribute name="type">
                        <xsl:text>context-name</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="ana">
                        <xsl:text>context-name</xsl:text>
                    </xsl:attribute>
                </xsl:when>
                <xsl:when test="normalize-space(.) = 'latex'">
                    <xsl:attribute name="type">
                        <xsl:text>latex-name</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="ana">
                        <xsl:text>latex-name</xsl:text>
                    </xsl:attribute>
                </xsl:when>
                <xsl:when test="normalize-space(.) = 'tex'">
                    <xsl:attribute name="type">
                        <xsl:text>tex-name</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="ana">
                        <xsl:text>tex-name</xsl:text>
                    </xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:message>
                        <xsl:text>No match for ab with value of </xsl:text>
                        <xsl:value-of select = "."/>
                    </xsl:message>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="revision/author"/>

    <xsl:template match="revision">
        <xsl:element name="note">
            <xsl:attribute name="ana">
                <xsl:text>revision</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="type">
                <xsl:text>revision</xsl:text>
            </xsl:attribute>
            <xsl:element name="bibl">
                <xsl:element name="author">
                    <xsl:value-of select="author"/>
                </xsl:element>
                <xsl:element name="date">
                    <xsl:value-of select="date"/>
                </xsl:element>
            </xsl:element>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="divGen[@type='table1']">
        <xsl:apply-templates select = "document('/home/paul/Documents/context/tables/table1.xml')"/>
    </xsl:template>

    
</xsl:stylesheet>
