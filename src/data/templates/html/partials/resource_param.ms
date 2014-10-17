{{# items.0 }}
<table>
  {{# items }}
  <tr>
    <td>
      <var><b>{{ prefix }}{{ name }}</b></var>
      <sup>{{# required }}*{{/ required }}</sup>
    </td>
    <td>
       {{# type }}<i>{{ type }}</i>{{/ type }}
    </td>
    <td>
      {{# description }}{{ description }}{{/ description }}
    </td>
    </tr>
  {{/ items }}
</table>
{{# items.0.required }}<p><sup><b>*</b></sup> indicates a required parameter.</p>{{/ items.0.required }}
{{/ items.0 }}