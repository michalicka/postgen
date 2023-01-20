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

export default parseJsonStream
