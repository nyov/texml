<x:stylesheet version='1.0' xmlns:x='http://www.w3.org/1999/XSL/Transform'>
<!-- $Id -->

<x:output mode="text" omit-xml-declaration="yes"/>

<x:template match="/">
	<x:text>textmap = {&#xa;</x:text>
	<x:apply-templates select="/charlist/character" mode="text"/>
	<x:text>}&#xa;mathmap = {&#xa;</x:text>
	<x:apply-templates select="/charlist/character" mode="math"/>
	<x:text>}&#xa;</x:text>
</x:template>

<!--
: 122 is a decimal code of letter 'z'
: There are no need to escape codes below this value
-->

<x:template match="character[latex and (@dec &gt; 122)]" mode="text">
	<x:call-template name="mapitem">
		<x:with-param name="code" select="@id"/>
		<x:with-param name="text" select="latex"/>
	</x:call-template>
</x:template>

<x:template match="character[mathlatex and (@dec &gt; 122)]" mode="math">
	<x:call-template name="mapitem">
		<x:with-param name="code" select="@id"/>
		<x:with-param name="text" select="mathlatex"/>
	</x:call-template>
</x:template>

<x:template name="mapitem">
	<x:param name="code"/>
	<x:param name="text"/>
	<x:variable name="quot">
		<x:choose>
			<x:when test="contains($text,&quot;'&quot;)">
				<x:text>"</x:text>
			</x:when>
			<x:otherwise>
				<x:text>'</x:text>
			</x:otherwise>
		</x:choose>
	</x:variable>
	<x:value-of select="
		concat('0x',
		substring-after($code,'U'),
		': r', $quot, $text, $quot, ',&#xa;')"/>
</x:template>

<x:template match="text()"/>
<x:template match="text()" mode="text"/>
<x:template match="text()" mode="math"/>

</x:stylesheet>