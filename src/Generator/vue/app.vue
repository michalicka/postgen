<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">

                        <div class=" mb-3">
                            <label for="title" class="form-label">Title of the article:</label>
                            <div class="input-group">
                              <input ref="title" id="title" type="text" class="form-control" v-model="title" @keydown.enter="submit">
                              <button type="button" class="btn btn-secondary" @click="submit"><arrow /></button>
                            </div>
                        </div>

                        <div class="mb-3">
                          <label for="content" class="form-label">Content of the article:</label>
                          <textarea ref="content" id="content" :style="style" class="form-control" v-model="content" @keydown="resize"></textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
async function *parseJsonStream(readableStream) {
    const regexp = new RegExp('({.*})', 'gms');
    for await (const line of readLines(readableStream.getReader())) {
        let trimmedLine = line.trim().replace(/,$/, '');
        trimmedLine = regexp.test(trimmedLine) ? trimmedLine.match(regexp)[0] : trimmedLine;

        if (trimmedLine !== '[]' && trimmedLine !== '\n') {
            try {
                yield JSON.parse(trimmedLine);
            } catch (e) {
            }
        }
    }
}

async function *readLines(reader) {
    const textDecoder = new TextDecoder();
    let partOfLine = '';
    for await (const chunk of readChunks(reader)) {
        const chunkText = textDecoder.decode(chunk);
        const chunkLines = chunkText.split('\n');
        if (chunkLines.length === 1) {
            partOfLine += chunkLines[0];
        } else if (chunkLines.length > 1) {
            yield partOfLine + chunkLines[0];
            for (let i=1; i < chunkLines.length - 1; i++) {
                yield chunkLines[i];
            }
            partOfLine = chunkLines[chunkLines.length - 1];
        }
    }
}

function readChunks(reader) {
    return {
        async* [Symbol.asyncIterator]() {
            let readResult = await reader.read();
            while (!readResult.done) {
                yield readResult.value;
                readResult = await reader.read();
            }
        },
    };
}

import Arrow from './arrow.vue';

export default {
    components: { Arrow },
    props: {
        apiUrl: {
            type: String,
            required: true,
        },
        apiUser: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            title: '',
            content: '',
            height: 24,
            model: '',
        }
    },
    computed: {
        style() {
            return `height: ${this.height}px;overflow-y:hidden;`;
        }
    },
    methods: {
        parseJsonStream,
        resize() {
            this.height = document.getElementById("content").scrollHeight;
        },
        submit() {
            const q=this.title || this.content;
            if (q) this.process(q);
        },
        process(q) {
            this.content = '';
            fetch(`${this.apiUrl}/?q=${encodeURIComponent(q)}&userid=${this.apiUser}`)
                .then(async (response) => {
                    // response.body is a ReadableStream
                    for await (const chunk of this.parseJsonStream(response.body)) {
                        this.model = chunk.model || '';
                        if (!Array.isArray(chunk) && !chunk.choices[0].finish_reason) {
                            this.append(chunk.choices[0].text || '');
                        } else {
                            this.store();
                        }
                    }
                });
        },
        append(text) {
            this.content = `${this.content}${text}`.trimStart();
            this.resize();
        },
        store() {
            axios.post('/store', {
                title: this.title,
                content: this.content,
                model: this.model,
            }).then(({data}) => {
                //console.log(data);
            });
        }
    },
    mounted() {
        this.$refs.title.focus();
    }
}
</script>
