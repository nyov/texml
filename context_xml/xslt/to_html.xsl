<xsl:stylesheet 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:exsl="http://exslt.org/common"
    xmlns:tei="http://www.tei-c.org/ns/1.0"
    extension-element-prefixes="exsl"
    version="1.1"
    exclude-result-prefixes = "exsl "
>
    <xsl:output method="xml" encoding="ASCII"/>

    <!--variables-->

    <!--where to put the pages-->
    <!--
    <xsl:variable name="output-dir" select="'/home/paul/Documents/context/html/'"/>
    -->
    <xsl:variable name="output-dir" select="'./'"/>

    <!--html suffix-->
    <xsl:variable name="html-suffix" select="'.html'"/>

    <!--title for index page-->
    <xsl:variable name="index-title" select="'index'"/>

    <!--is it a draft?-->
    <xsl:variable name="draft" select="'true'"/>


    <!--number of total pages-->
    <xsl:variable name="total-pages" >
        <xsl:value-of select = "count(//tei:body/tei:div)"/>
    </xsl:variable>

    <!--root-->

    <xsl:template match = "/">
        <xsl:call-template name="make-index"/>
        <xsl:apply-templates/>
    </xsl:template>


    <!--make toc for index page-->
    <xsl:template name = "make-index">
        <xsl:variable name="output">
            <xsl:value-of select="$output-dir"/>
            <xsl:text>index</xsl:text>
            <xsl:value-of select = "$html-suffix"/>
        </xsl:variable>
        <xsl:document href="{$output}" method="xml">
            <html>
                <head>
                    <title><xsl:value-of select = "$index-title"/></title>
                </head>
                <xsl:element name="link">
                    <xsl:attribute name="rel">
                        <xsl:text>stylesheet</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="type">
                        <xsl:text>text/css</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="href">
                        <xsl:text>styles.css</xsl:text>
                    </xsl:attribute>
                </xsl:element>
                <body>
                    <h1 class="index-head">Welcome to XML ConTeXt</h1>
                    <xsl:element name="div">
                        <xsl:attribute name="class">
                            <xsl:text>index-toc</xsl:text>
                        </xsl:attribute>
                        <xsl:for-each select = "/tei:TEI/tei:text/tei:body/tei:div">
                            <xsl:element name="p">
                                <xsl:attribute name="class">
                                    <xsl:text>toc-external</xsl:text>
                                </xsl:attribute>
                                <xsl:element name="a">
                                    <xsl:attribute name="href">
                                        <xsl:text>page</xsl:text>
                                        <xsl:value-of select="count(preceding-sibling::*) + 1"/>
                                        <xsl:value-of select = "$html-suffix"/>
                                    </xsl:attribute>
                                    <xsl:apply-templates select="tei:head" mode="toc-index"/>
                                </xsl:element>
                            </xsl:element>
                            <xsl:apply-templates select="tei:div" mode="toc-index"/>
                        </xsl:for-each>
                    </xsl:element>
                </body>
            </html>
        </xsl:document>
    </xsl:template>


    <!--div for toc on index page-->
    <xsl:template match="tei:div" mode="toc-index">
        <xsl:element name="p">
            <xsl:attribute name="class">
                <xsl:text>index-toc-level</xsl:text>
                <xsl:value-of select="count(ancestor::tei:div)"/>
            </xsl:attribute>
            <xsl:element name="a">
                <xsl:attribute name="href">
                    <xsl:text>page</xsl:text>
                    <xsl:for-each select="ancestor::tei:div[last()]">
                        <xsl:value-of select="count(preceding-sibling::*) + 1"/>
                    </xsl:for-each>
                    <xsl:value-of select = "$html-suffix"/>
                    <xsl:text>#</xsl:text>
                    <xsl:value-of select="generate-id(.)"/>
                </xsl:attribute>
                <xsl:apply-templates select="tei:head" mode="toc"/>
            </xsl:element>
        </xsl:element>
        <xsl:apply-templates select="tei:div" mode="toc-index"/>
    </xsl:template>

    <xsl:template match="tei:TEI">
        <xsl:apply-templates/>
    </xsl:template>

    <!--teiHeader-->
    <!--for now, do nothing with-->
    <xsl:template match ="tei:teiHeader"/>

    <!--text-->
    <xsl:template match="tei:text">
        <xsl:apply-templates/>
    </xsl:template>

    <!--body-->
    <xsl:template match="tei:body">
        <xsl:apply-templates/>
    </xsl:template>



    <!--main div which we break up into pages-->
  <xsl:template match="tei:body/tei:div">
    <xsl:variable name="output">
        <xsl:value-of select="$output-dir"/>
        <xsl:text>page</xsl:text>
        <xsl:value-of select="count(preceding-sibling::*) + 1"/>
        <xsl:value-of select = "$html-suffix"/>
    </xsl:variable>
    <xsl:document href="{$output}" method="xml">
        <html>
            <head>
                <title><xsl:value-of select="tei:head"/></title>
                <xsl:element name="link">
                    <xsl:attribute name="rel">
                        <xsl:text>stylesheet</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="type">
                        <xsl:text>text/css</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="href">
                        <xsl:text>styles.css</xsl:text>
                    </xsl:attribute>
                </xsl:element>
            </head>
            <body>
                <xsl:apply-templates/>
                <xsl:call-template name="page-links"/>
                <xsl:call-template name="logo"/>
                <xsl:call-template name="copyright"/>
            </body>
        </html>
    </xsl:document>
  </xsl:template>

  <!--get rid of bug pages-->
  <xsl:template match="tei:div[@type='unsure']" priority="10"/>

  <!--page-links template-->
  <xsl:template name="page-links">
    <xsl:variable name="page-number">
        <xsl:value-of select="count(preceding-sibling::*) + 1"/>
    </xsl:variable>
    <xsl:element name="p">
        <xsl:element name="a">
            <xsl:attribute name="href">
                <xsl:text>index</xsl:text>
                <xsl:value-of select="$html-suffix"/>
            </xsl:attribute>
            <xsl:text>home | </xsl:text>
        </xsl:element>
        <xsl:choose>
            <xsl:when test="$page-number = '1'">
                <xsl:text>previous | </xsl:text>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="a">
                    <xsl:attribute name="href">
                        <xsl:text>page</xsl:text>
                        <xsl:value-of select="$page-number - 1"/>
                        <xsl:value-of select = "$html-suffix"/>
                    </xsl:attribute>
                    <xsl:text>previous | </xsl:text>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>
        <xsl:choose>
            <xsl:when test = "$page-number = $total-pages">
                <xsl:text>next</xsl:text>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="a">
                    <xsl:attribute name="href">
                        <xsl:text>page</xsl:text>
                        <xsl:value-of select = "$page-number + 1"/>
                        <xsl:value-of select = "$html-suffix"/>
                    </xsl:attribute>
                    <xsl:text>next</xsl:text>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:element>
      
  </xsl:template>

  <!--logo (for sourceforge)-->
  <xsl:template name="logo">
      
  </xsl:template>

  <!--copyright-->
  <xsl:template name="copyright">
  <p>
 copyright 2005 Paul Henry Tremblay
      
  </p>
  <p>
      License: GPL
  </p>
      
  </xsl:template>

  <!--for now, get rid of main section head-->
  <xsl:template match="tei:body/tei:div/tei:head">
      <xsl:element name="h2">
          <xsl:attribute name="class">
              <xsl:text>page-head</xsl:text>
          </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--but not for the index-->
  <xsl:template match="tei:body/tei:div/tei:head" mode="toc-index">
      <xsl:apply-templates/>
  </xsl:template>

  <xsl:template match="tei:head" mode = "toc">
      <xsl:apply-templates/>
  </xsl:template>

  <!--for now, get rid of all the revision history for each main division-->
  <xsl:template match="tei:bibl"/>

  <!--div 1 level-->
  <xsl:template match="tei:body/tei:div/tei:div">
      <xsl:element name="div">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>1</xsl:text>
          </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--div head 1 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:head">
      <xsl:element name="h3">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>1</xsl:text>
          </xsl:attribute>
          <xsl:element name="a">
              <xsl:attribute name="name">
                  <xsl:value-of select="generate-id(.)"/>
              </xsl:attribute>
          </xsl:element>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--div 2 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:div">
      <xsl:element name="div">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>2</xsl:text>
          </xsl:attribute>
          <xsl:element name="a">
              <xsl:attribute name="name">
                  <xsl:value-of select="generate-id(.)"/>
              </xsl:attribute>
          </xsl:element>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--div head 2 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:div/tei:head">
      <xsl:element name="h3">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>2</xsl:text>
          </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>


  <!--div 3 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:div/tei:div">
      <xsl:element name="div">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>3</xsl:text>
          </xsl:attribute>
          <xsl:element name="a">
              <xsl:attribute name="name">
                  <xsl:value-of select="generate-id(.)"/>
              </xsl:attribute>
          </xsl:element>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--div head 3 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:div/tei:div/tei:head">
      <xsl:element name="h3">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>3</xsl:text>
          </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--div 4 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:div/tei:div/tei:div">
      <xsl:element name="div">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>4</xsl:text>
          </xsl:attribute>
          <xsl:element name="a">
              <xsl:attribute name="name">
                  <xsl:value-of select="generate-id(.)"/>
              </xsl:attribute>
          </xsl:element>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--div head 4 level-->
  <xsl:template match="tei:body/tei:div/tei:div/tei:div/tei:div/tei:div/tei:head">
      <xsl:element name="h3">
          <xsl:attribute name="class">
              <xsl:text>level</xsl:text>
                <xsl:text>4</xsl:text>
          </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--divGen-->
  <!--divGen will generally be used to reference external example documents-->
  <!--for now, am not sure what to do with these documents-->
  <xsl:template match="tei:divGen[@type='simple1_tex']"/>
  <xsl:template match="tei:divGen[@type='simple1_texml']"/>
  <xsl:template match="tei:divGen[@type='page_setup1_tex']"/>
  <xsl:template match="tei:divGen[@type='page_setup1_texml']"/>


  <!--generic p-->
  <xsl:template match = "tei:p">
      <xsl:element name="p">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--tables-->
  <xsl:template match="tei:table">
      <xsl:element name="table">
        <xsl:choose>
            <xsl:when test="tei:row[@role='label']">
                <xsl:element name="thead">
                    <xsl:apply-templates select="tei:row[@role='label']" mode="table-head"/>
                </xsl:element>
            </xsl:when>
        </xsl:choose>
        <xsl:element name="tbody">
            <xsl:apply-templates/>
        </xsl:element>
      </xsl:element>
  </xsl:template>

  <!--row with label-->
  <!--get rid of since it is alreay processed in thead-->
  <xsl:template match="tei:row[@role='label']"/>

  <!--actually process the header row-->
  <xsl:template match="tei:row[@role='label']" mode="table-head">
      <xsl:element name="tr">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--row-->
  <xsl:template match="tei:row">
      <xsl:element name="tr">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--cell-->
  <xsl:template match="tei:cell">
      <xsl:element name="td">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--table head-->
  <xsl:template match="tei:table/tei:head">
      <caption align="bottom">
          <xsl:apply-templates/>
      </caption>
  </xsl:template>

  <!--block code-->
  <xsl:template match="tei:eg[@rend='context-block-code']|tei:eg[@rend='xml']">
    <xsl:element name="div">
        <xsl:attribute name="class">
            <xsl:value-of select="@rend"/>
        </xsl:attribute>
          <xsl:element name="pre">
              <xsl:apply-templates/>
          </xsl:element>
    </xsl:element>
  </xsl:template>

  <!--lists-->

  <!--ordered list-->
  <xsl:template match="tei:list[@type='ordered']">
      <xsl:element name="ol">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--undorederd list-->
  <xsl:template match="tei:list[@type='unordered']">
    <xsl:apply-templates select="tei:head" mode="list"/>
      <xsl:element name="ul">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--item in list-->
  <xsl:template match="tei:item">
      <xsl:element name="li">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <!--head in list-->
  <xsl:template match="tei:list/tei:head"/>
  <xsl:template match="tei:list/tei:head" mode="list">
    <xsl:choose>
        <xsl:when test="normalize-space(.) = 'Tips'">
            <xsl:element name="h3">
                <xsl:attribute name="class">
                    <xsl:text>list-head</xsl:text>
                </xsl:attribute>
                <xsl:apply-templates/>
            </xsl:element>
        </xsl:when>
    </xsl:choose>
  </xsl:template>

  <!--figure-->
  <xsl:template match="tei:figure">
      <xsl:variable name="path">
          <xsl:value-of select = "concat('png_images/', substring-before(@url, '.'), '.png')"/>
      </xsl:variable>
      <xsl:element name="div">
          <xsl:attribute name="class">
              <xsl:text>img</xsl:text>
          </xsl:attribute>
          <xsl:element name="img">
              <xsl:attribute name="src">
                  <xsl:value-of select="$path"/>
              </xsl:attribute>
              <xsl:attribute name="alt">
                  <xsl:value-of select="tei:head"/>
              </xsl:attribute>
              <xsl:apply-templates/>
          </xsl:element>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:figure/tei:head">
      <xsl:element name="p">
        <xsl:attribute name="class">
            <xsl:text>figure-caption</xsl:text>
        </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>
    
  <!--inline-->


  <!--xref-->
  <xsl:template match="tei:xref[@url]">
      <xsl:element name="a">
          <xsl:attribute name="href">
              <xsl:value-of select="@url"/>
          </xsl:attribute>
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match = "tei:emph[@rend='italic']">
      <xsl:element name="i">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:emph[@rend='bold']">
      <xsl:element name="b">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:seg[@type='code']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:seg[@type='latex-name']">
      <xsl:text>LaTeX</xsl:text>
  </xsl:template>

  <xsl:template match="tei:seg[@type='tex-name']">
      <xsl:text>TeX</xsl:text>
  </xsl:template>

  <xsl:template match="tei:seg[@type='context-name']">
      <xsl:text>ConTeXt</xsl:text>
  </xsl:template>

  <xsl:template match="tei:seg[@type='context-command']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:seg[@type='property']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:eg[@rend='command']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:eg[@rend='context-code']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="tei:seg[@type='option']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>


  <xsl:template match="tei:seg[@type='command']">
      <xsl:element name="tt">
          <xsl:apply-templates/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="*">
      <xsl:if test="$draft='true'">
          <h1><xsl:value-of select="name(.)"/></h1>
          <xsl:message>
              <xsl:text>no value for </xsl:text>
              <xsl:value-of select="name(.)"/>
          </xsl:message>
      </xsl:if>
      <xsl:apply-templates/>
  </xsl:template>

</xsl:stylesheet>
