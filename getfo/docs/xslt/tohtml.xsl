<x:stylesheet version="1.0" xmlns:x="http://www.w3.org/1999/XSL/Transform">
<!-- creates HTML from a pre-HTML -->
<x:output method="xml"/>

<x:template match="node()|@*">
	<x:copy>
		<x:apply-templates select="node()|@*" />
	</x:copy>
</x:template>

<x:template match="doc">
	<html id="{@id}">
		<head>
			<title>
				<x:value-of select="title" />
			</title>
		</head>
		<body>
			<x:apply-templates select="node()" />
		</body>
	</html>
</x:template>

<x:template match="sect">
	<x:apply-templates select="node()|@*" />
</x:template>

<x:template match="title">
	<x:element name="{concat('h',count(ancestor::sect)+1)}">
		<x:apply-templates select="node()|@*" />
	</x:element>
</x:template>

<x:template match="lst">
	<pre>
		<x:apply-templates select="node()|@*" />
	</pre>
</x:template>

<x:template match="e">
	<code>
		<x:apply-templates select="node()|@*" />
	</code>
</x:template>

</x:stylesheet>
