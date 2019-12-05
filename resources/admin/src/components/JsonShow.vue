<template>
  <pre v-html="jsonHtml"/>
</template>

<script>
export default {
  name: 'JsonShow',
  props: {
    json: null,
    isString: Boolean,
  },
  computed: {
    jsonHtml() {
      let json = this.json
      if (!this.isString) {
        json = JSON.stringify(json, null, 2)
      }

      return this.toJsonHtml(json)
    },
  },
  methods: {
    toJsonHtml(json) {
      json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        let cls = 'number'
        if (/^"/.test(match)) {
          if (/:$/.test(match)) {
            cls = 'key'
          } else {
            cls = 'string'
          }
        } else if (/true|false/.test(match)) {
          cls = 'boolean'
        } else if (/null/.test(match)) {
          cls = 'null'
        }

        return `<span class="${cls}">${match}</span>`
      })
    },
  },
}
</script>

<style scoped lang="scss">
pre {
  margin: 0;
  overflow-x: auto;
}

::v-deep {
  .string {
    color: green;
  }

  .number {
    color: darkorange;
  }

  .boolean {
    color: blue;
  }

  .null {
    color: magenta;
  }

  .key {
    color: red;
  }
}
</style>
