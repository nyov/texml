<xsl:stylesheet
   xmlns:tei="http://www.tei-c.org/ns/1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
   exclude-result-prefixes="tei "
  version="1.0"
>

<xsl:output method="xml"/>

<xsl:variable name="page-layout1" select = "document(examples/page_layout.xml)"/>

<xsl:template match="/">
    <xsl:apply-templates/>
</xsl:template>


<xsl:template match="tei:body/tei:div">
    <xsl:variable name="temp">
        <xsl:text>/home/paul/Documents/context/wiki/</xsl:text>
        <xsl:value-of select = "tei:head"/>
    </xsl:variable>
    <xsl:variable name="name">
        <xsl:call-template name="parse_doc_name">
            <xsl:with-param name="the_string" select = "$temp"/>
        </xsl:call-template>
    </xsl:variable>
    <xsl:document href = "{$name}">
        <doc>
            <xsl:apply-templates/>
        </doc>
    </xsl:document>
</xsl:template>


<xsl:template match="tei:div/tei:head">
    <block>
        <xsl:variable name="level">
            <xsl:value-of select="count(ancestor::tei:div) -1 "/>
        </xsl:variable>
        <xsl:call-template name="put-in-equal-signs">
            <xsl:with-param name = "number" select = "$level"/>
        </xsl:call-template>
        <xsl:if test = "not($level = 0)">
            <xsl:apply-templates/>
        </xsl:if>
        <xsl:call-template name="put-in-equal-signs">
            <xsl:with-param name = "number" select = "$level"/>
        </xsl:call-template>
    </block>
</xsl:template>

<xsl:template match="tei:list/tei:item/tei:p">
    <block new-lines-after="1" normalize-space="false">
        <xsl:apply-templates/>
    </block>
</xsl:template>

<xsl:template match="tei:list[@type='unordered']/tei:item">
    <xsl:variable name="number" select = "count(ancestor::tei:list[@type='unordered'])"/>
    <block space-after = "1" new-lines-after="0">
        <xsl:choose>
            <xsl:when test = "$number = 1">
                <xsl:text>*</xsl:text>
            </xsl:when>
            <xsl:when test = "$number = 2">
                <xsl:text>**</xsl:text>
            </xsl:when>
        </xsl:choose>
    </block>
    <xsl:apply-templates/>
</xsl:template>

<xsl:template match="tei:list">
    <xsl:apply-templates/>
    <block/>
</xsl:template>

<xsl:template match="tei:eg[@rend='context']|tei:eg">
    <block>
        <xsl:text>&lt;texcode&gt;</xsl:text>
    </block>
    <block literal = "true">
        <xsl:apply-templates/>
    </block>

    <block>
        <xsl:text>&lt;/texcode&gt;</xsl:text>
    </block>
</xsl:template>

<xsl:template match="tei:eg[@rend='xml']">
    <block>
        <xsl:text>&lt;pre&gt;</xsl:text>
    </block>
    <block literal = "true">
        <xsl:apply-templates/>
    </block>

    <block>
        <xsl:text>&lt;/pre&gt;</xsl:text>
    </block>
</xsl:template>

<xsl:template match="tei:p">
    <block>
        <xsl:apply-templates/>
    </block>
</xsl:template>

<!--inline-->

<xsl:template match="tei:empt[@rend='italic']">
    <xsl:text>''</xsl:text>
    <xsl:apply-templates/>
    <xsl:text>''</xsl:text>
</xsl:template>

<xsl:template match="tei:empt[@rend='bold']">
    <xsl:text>'''</xsl:text>
    <xsl:apply-templates/>
    <xsl:text>'''</xsl:text>
</xsl:template>

<xsl:template match = "tei:seg[@type='context-name']">
    <!--
    <xsl:text>&lt;tt&gt;</xsl:text>
    -->
    <xsl:text>ConTeXt</xsl:text>
    <!--
    <xsl:text>&lt;/tt&gt;</xsl:text>
    -->
</xsl:template>

<xsl:template match = "tei:seg[@type='command']">
    <xsl:text>&lt;tt&gt;</xsl:text>
    <xsl:text>\</xsl:text>
    <xsl:apply-templates/>
    <xsl:text>&lt;/tt&gt;</xsl:text>
</xsl:template>

<xsl:template match = "tei:seg[@type='option']">
    <xsl:text>&lt;tt&gt;</xsl:text>
    <xsl:apply-templates/>
    <xsl:text>&lt;/tt&gt;</xsl:text>
</xsl:template>

<!--example documents-->

<xsl:template match="tei:divGen[@type='page_setup1_tex']">
    <block>
    [[page setup1]]
    </block>
</xsl:template>

<xsl:template match="tei:divGen[@type='page_setup1_texml']">
    <block>
    [[page setup1 texml]]
    </block>
</xsl:template>

<xsl:template match="tei:divGen[@type='simple1_tex']">
    <block>
[[simple_page.tex]]
    </block>
</xsl:template>

<xsl:template match="tei:divGen[@type='simple1_texml']">
    <block>
    [[simple_page.texml]]
    </block>
</xsl:template>

<xsl:template match="tei:xref[@url]">
    <xsl:text> </xsl:text>
    <xsl:value-of select = "@url"/>
    <xsl:text> </xsl:text>
    <xsl:apply-templates/>
</xsl:template>

<xsl:template match="tei:divGen">
    <xsl:message>
        <xsl:text>no target for divGen with </xsl:text> 
        <xsl:value-of select="@type"/>
    </xsl:message>
</xsl:template>

<xsl:template name="put-in-equal-signs">
    <xsl:param name="number"/>
    <xsl:choose>
        <xsl:when test = "$number=0"/>
        <xsl:when test = "$number=1">
            <xsl:text>=</xsl:text>
        </xsl:when>
        <xsl:when test="$number=2">
            <xsl:text>==</xsl:text>
        </xsl:when>
        <xsl:when test="$number=3">
            <xsl:text>===</xsl:text>
        </xsl:when>
        <xsl:when test="$number=4">
            <xsl:text>====</xsl:text>
        </xsl:when>
        <xsl:when test="$number=5">
            <xsl:text>=====</xsl:text>
        </xsl:when>
        <xsl:otherwise>
            <xsl:message>
               no match for equal signs 
               <xsl:value-of select ="$number"/>
            </xsl:message>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template name = "parse_doc_name">
    <xsl:param name="the_string"/>
    <xsl:param name="the_finished_string"/>
    <xsl:variable name="before">
        <xsl:value-of select = "substring-before($the_string, ' ')"/>
    </xsl:variable>
    <xsl:choose>
        <xsl:when test = "normalize-space($the_string)">
            <xsl:choose>
                <xsl:when test = "normalize-space($before)">
                    <xsl:call-template name="parse_doc_name">
                        <xsl:with-param name = "the_string">
                            <xsl:value-of select = "substring-after($the_string, ' ')"/>
                        </xsl:with-param>
                        <xsl:with-param name="the_finished_string">
                            <xsl:value-of select = "$the_finished_string"/>
                            <xsl:value-of select="substring-before($the_string,' ')"/>
                            <xsl:text>_</xsl:text>
                        </xsl:with-param>
                    </xsl:call-template>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="$the_finished_string"/>
                    <xsl:value-of select="$the_string"/>
                    <xsl:text>.xml</xsl:text>

                </xsl:otherwise>
            </xsl:choose>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select = "$the_finished_string"/>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!--
==============revisions (note element)==========
-->

<xsl:template match="tei:note[@type='revision']"/>

<!--
=======TABLE=================
-->
  
<xsl:template match="tei:table">
    <block>
        &lt;table border="2" &gt;
    </block>
        <xsl:apply-templates/>
    <block>
        &lt;/table&gt;
    </block>
</xsl:template>

<xsl:template match="tei:row">
    <block>
        &lt;tr&gt;
    </block>
        <xsl:apply-templates/>
    <block>
        &lt;/tr&gt;
    </block>
</xsl:template>

<xsl:template match="tei:cell[@role='label']">
    <block>
        &lt;th&gt;
    </block>
        <xsl:apply-templates/>
    <block>
        &lt;/th&gt;
    </block>
</xsl:template>

<xsl:template match="tei:cell">
    <block>
        &lt;td&gt;
    </block>
        <xsl:apply-templates/>
    <block>
        &lt;/td&gt;
    </block>
</xsl:template>

<xsl:template match="tei:table/tei:head">
    <block>
        &lt;caption&gt;
    </block>
        <xsl:apply-templates/>
    <block>
        &lt;/caption&gt;
    </block>
</xsl:template>


</xsl:stylesheet>
