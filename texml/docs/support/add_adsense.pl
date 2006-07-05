use strict;

my $added = 0;
my $html = qq{<script type="text/javascript"><!--
google_ad_client = "pub-8908956343180748";
google_alternate_color = "FFFFFF";
google_ad_width = 468;
google_ad_height = 60;
google_ad_format = "468x60_as";
google_ad_type = "text_image";
google_ad_channel ="0926342787";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<hr>
};

while (defined (my $l = <>)) {
  if ($l =~ m/^<div class="footnote">/) {
    die "two footnotes" if $added;
    $added = 1;
    print $html;
  }
  print $l;
}

die "no footnotes" unless $added;

0;
