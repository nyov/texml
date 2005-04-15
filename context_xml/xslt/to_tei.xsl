<xsl:stylesheet 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:exsl="http://exslt.org/common"
    xmlns:tei="http://www.tei-c.org/ns/1.0"
    xmlns:tx= "http://xml2txt.sourceforge.net/ns1"
    extension-element-prefixes="exsl"
    version="1.1"
    exclude-result-prefixes = "exsl tx"
>
    <xsl:import href= "/home/paul/cvs/txt2xml/xsl_stylesheets/str_to_tei_ns.xsl"/>
    <xsl:output method="xml" encoding="ASCII"/>
    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="tei:*">
        <xsl:variable name="name">
            <xsl:text>tei:</xsl:text>
            <xsl:value-of select="name(.)"/>
        </xsl:variable>
        <xsl:element name="{$name}">
            <xsl:copy-of select = "@*"/>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>


    <xsl:template match="tx:t2x-no-process">
        <xsl:element name="tei:eg">
            <xsl:choose>
                <xsl:when test = "@rend">
                    <xsl:attribute name="rend">
                        <xsl:value-of select="@rend"/>
                    </xsl:attribute>
                    <xsl:attribute name="ana">
                        <xsl:value-of select="@rend"/>
                    </xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:attribute name="rend">
                        <xsl:text>context-block-code</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="ana">
                        <xsl:text>context-block-code</xsl:text>
                    </xsl:attribute>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:apply-templates/>
        </xsl:element>
        
    </xsl:template>

    <xsl:template match="tx:eg">
        <xsl:element name="tei:eg">
            <xsl:choose>
                <xsl:when test = "@rend">
                    <xsl:attribute name="rend">
                        <xsl:value-of select = "@rend"/>
                    </xsl:attribute>
                    <xsl:attribute name="ana">
                        <xsl:text>code</xsl:text>
                    </xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:attribute name="rend">
                        <xsl:text>context-code</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="ana">
                        <xsl:text>context-code</xsl:text>
                    </xsl:attribute>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tx:doc">
            <xsl:apply-templates select="tx:change" mode="doc-start"/>
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="tx:change" mode="doc-start">
        <xsl:element name="tei:bibl">
            <xsl:attribute name="ana">
                <xsl:text>section-revision</xsl:text>
            </xsl:attribute>
            <xsl:element name="tei:date">
                <xsl:attribute name="value">
                    <xsl:value-of select = "@date"/>
                </xsl:attribute>
            </xsl:element>
            <xsl:apply-templates mode = "doc-start"/>
        </xsl:element>
    </xsl:template>

    <xsl:template match = "tx:change/tx:p" mode="doc-start">
        <xsl:element name="tei:note">
            <xsl:attribute name="type">
                <xsl:text>revision-description</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="ana">
                <xsl:text>paragraph</xsl:text>
            </xsl:attribute>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tx:change|tx:author|tx:doc/tx:title"/>

    <xsl:template match="tx:title">
        <!--
        <tei:head ana="head">
            <xsl:apply-templates/>
        </tei:head>
        -->

        <xsl:variable name="title">
            <xsl:choose>
                <xsl:when test = "contains(., '[:ab: context]')">
                    <xsl:value-of select= "substring-before(., '[:ab: context]')"/>
                    <tx:ab>context</tx:ab>
                    <xsl:value-of select= "substring-after(., '[:ab: context]')"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="."/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <tei:head ana="head">
            <xsl:apply-templates select = "exsl:node-set($title)"/>
        </tei:head>

    </xsl:template>

    <xsl:template match="tx:section[@sp='1']/tx:title">
        <xsl:element name="tei:head">
            <xsl:attribute name="ana">
                <xsl:text>head</xsl:text>
            </xsl:attribute>
            Simple Document in <tei:seg type="context-name" ana="context-name">context</tei:seg> and in TeXML
        </xsl:element>
    </xsl:template>


    <xsl:template match="tx:section">
        <xsl:element name="tei:div">
            <xsl:choose>
                <xsl:when test="@bug='true'">
                    <xsl:attribute name="type">
                        <xsl:text>unsure</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="ana">
                        <xsl:text>unsure</xsl:text>
                    </xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:choose>
                        <xsl:when test="tx:title = 'Tips'">
                            <xsl:attribute name="ana">
                                <xsl:text>div-tips</xsl:text>
                            </xsl:attribute>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:attribute name="ana">
                                <xsl:text>div</xsl:text>
                            </xsl:attribute>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tx:cm">
        <xsl:element name="tei:seg">
            <xsl:attribute name="type">
                <xsl:text>command</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="ana">
                <xsl:text>context-command</xsl:text>
            </xsl:attribute>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tx:op">
        <xsl:element name="tei:seg">
            <xsl:attribute name="type">
                <xsl:text>option</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="ana">
                <xsl:text>option</xsl:text>
            </xsl:attribute>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tx:pr">
        <xsl:element name="tei:seg">
            <xsl:attribute name="type">
                <xsl:text>property</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="ana">
                <xsl:text>property</xsl:text>
            </xsl:attribute>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tx:ab">
        <xsl:element name="tei:seg">
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

    <xsl:template match="tx:url">
        <xsl:element name = "tei:xref">
            <xsl:choose>
                <xsl:when test="normalize-space(.) = 'texml'">
                    <xsl:attribute name="url">
                        <xsl:text>http://getfo.sourceforge.net/texml/index.html</xsl:text>
                    </xsl:attribute>
                    <xsl:text>TeXML</xsl:text>
                </xsl:when>
                <xsl:otherwise>
                        <xsl:message>
                            <xsl:text>No match for url with value of </xsl:text>
                            <xsl:value-of select = "."/>
                        </xsl:message>
                    
                </xsl:otherwise>
            </xsl:choose>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tx:xref">
        <xsl:element name="tei:xref">
            <xsl:attribute name="ana">
                <xsl:text>xref</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="url">
                <xsl:value-of select="@url"/>
            </xsl:attribute>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tx:src">
        <xsl:element name="tei:p">
            <xsl:attribute name="ana">
                <xsl:text>paragraph</xsl:text>
            </xsl:attribute>
            <xsl:element name="tei:xref">
                <xsl:attribute name="ana">
                    <xsl:text>xref</xsl:text>
                </xsl:attribute>
                <xsl:attribute name="url">
                    <xsl:value-of select="@url"/>
                </xsl:attribute>
                <xsl:apply-templates/>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tx:xr">
        <xsl:element name="tei:xref">
            <xsl:attribute name="url">
                <xsl:value-of select = "."/>
            </xsl:attribute>
            <xsl:attribute name="ana">
                <xsl:text>external-figure</xsl:text>
            </xsl:attribute>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="tx:divGen[@type='table1']">
        <xsl:apply-templates select = "document('/home/paul/Documents/context/tables/table1.xml')"/>
    </xsl:template>

    <xsl:template match="tx:tb">
        <xsl:variable name="url">
            <xsl:text>../tables/</xsl:text>
            <xsl:value-of select="@url"/>
            <xsl:text>.xml</xsl:text>
        </xsl:variable>
        <xsl:apply-templates select = "document($url)"/>
    </xsl:template>

    <xsl:template match="tx:revision">
        <xsl:element name="tei:bibl">
            <xsl:if test="tx:date">
                <xsl:element name="tei:date">
                    <xsl:attribute name="value">
                        <xsl:value-of select="."/>
                    </xsl:attribute>
                </xsl:element>
            </xsl:if>
            <xsl:attribute name="ana">
                <xsl:text>revision-in-text</xsl:text>
            </xsl:attribute>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tx:revision/tx:p">
        <xsl:element name="tei:note">
            <xsl:attribute name="type">
                <xsl:text>revision-description</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="ana">
                <xsl:text>paragraph</xsl:text>
            </xsl:attribute>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tx:revision/tx:author">
        <xsl:element name="tei:author">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tx:fig">
        <xsl:element name="tei:figure">
            <xsl:attribute name="url">
                <xsl:value-of select="@url"/>
                <xsl:text>.svg</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="ana">
                <xsl:text>figure</xsl:text>
            </xsl:attribute>
            <xsl:element name="tei:head">
                <xsl:attribute name="ana">
                    <xsl:text>head</xsl:text>
                </xsl:attribute>
                <xsl:apply-templates/>
            </xsl:element>
        </xsl:element>
        
    </xsl:template>


    <xsl:template match="tx:li">
        <tei:item ana="item">
        <xsl:apply-templates/>
        <xsl:call-template name="suck-in-blockquotes"/>
        </tei:item>
    </xsl:template>

    <xsl:template name="suck-in-blockquotes">
        <xsl:for-each select = "following-sibling::*[1]">
            <xsl:choose>
                <xsl:when test="self::tx:blockquote">
                    <xsl:apply-templates/>
                    <xsl:call-template name="suck-in-blockquotes"/>
                </xsl:when>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>

    <xsl:template match="tx:ul/tx:blockquote|tx:ol/tx:blockquote"/>

<xsl:template match = "tx:p">
    <xsl:element name="tei:p">
        <xsl:choose>
            <xsl:when test = "tx:rend">
                <xsl:attribute name="rend">
                    <xsl:value-of select="tx:rend"/>
                    <!--
                    <xsl:call-template name="make-rend-att">
                        <xsl:with-param name="the-string" select = "rend"/>
                    </xsl:call-template>
                    -->
                </xsl:attribute>
            </xsl:when>
        </xsl:choose>
        <xsl:attribute name="ana">
            <xsl:text>paragraph</xsl:text>
        </xsl:attribute>
        <xsl:apply-templates/>
    </xsl:element>
</xsl:template>

<xsl:template match="tx:ol">
    <xsl:element name="tei:list">
        <xsl:attribute name="type">
            <xsl:text>ordered</xsl:text>
        </xsl:attribute>
        <xsl:attribute name="ana">
            <xsl:text>ordered-list</xsl:text>
        </xsl:attribute>
        <xsl:apply-templates/>
    </xsl:element>
</xsl:template>
    

<xsl:template match="tx:ul">
    <xsl:element name="tei:list">
        <xsl:attribute name="type">
            <xsl:text>unordered</xsl:text>
        </xsl:attribute>
        <xsl:attribute name="ana">
            <xsl:text>unordered-list</xsl:text>
        </xsl:attribute>
        <xsl:apply-templates/>
    </xsl:element>
</xsl:template>

<xsl:template match="tx:emph[@rend='italics']">
    <tei:emph rend="italic" ana="emph-italic">
        <xsl:apply-templates/>
    </tei:emph>
</xsl:template>

<xsl:template match="tx:emph[@rend='bold']">
    <tei:emph rend="bold" ana="emph-bold">
        <xsl:apply-templates/>
    </tei:emph>
</xsl:template>

<xsl:template match="tx:ul/tx:head">
    <xsl:element name="tei:head">
        <xsl:attribute name="ana">
            <xsl:text>head</xsl:text>
        </xsl:attribute>
        <xsl:apply-templates/>
    </xsl:element>
</xsl:template>
<xsl:template match="tx:txt2xml-shortcut-xref"/>
<xsl:template match="tx:txt2xml-shortcuts-xref"/>

<xsl:template match="tx:txt2xmlabbr">
    <xsl:choose>
        <xsl:when test = "//tx:doc/tx:txt2xml-shortcuts-xref/tx:p/tx:txt2xml-shortcut-xref/@abbr = .">
            <xsl:element name="tei:xref">
                <xsl:attribute name="url">
                    <xsl:value-of select="//tx:doc/tx:txt2xml-shortcuts-xref/tx:p/tx:txt2xml-shortcut-xref/@expand"/>
                </xsl:attribute>
                <xsl:apply-templates/>
            </xsl:element>
        </xsl:when>
        <xsl:otherwise>
            <xsl:message>
                no value for abbr 
                <xsl:value-of select = "."/>
            </xsl:message>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template match="tx:status">
    <xsl:element name="tei:p">
        <xsl:attribute name="ana">
            <xsl:text>paragraph</xsl:text>
        </xsl:attribute>
        <xsl:attribute name="rend">
            <xsl:text>status</xsl:text>
        </xsl:attribute>
        <xsl:apply-templates/>
    </xsl:element>
</xsl:template>

<xsl:template match="tx:status-table">
    <xsl:apply-templates/>
</xsl:template>

</xsl:stylesheet>

