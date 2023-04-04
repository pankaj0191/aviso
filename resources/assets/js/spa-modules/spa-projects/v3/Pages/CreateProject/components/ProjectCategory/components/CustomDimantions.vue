<template>
    <label class="m-b-lg" style="width: 100%">
        <input type="radio"
            :value="val" 
            :name="name"
            :checked="val == value" 
            @change="$emit('input', val)" 
            class="card-input-element hidden">
        <div class="category-card">
            <div class="category-image">
                <slot name="image-svg"></slot>
            </div>
            <div class="category-title">{{title}}</div>
            <div class="custom-dimantions">
                <slot name="custom-input"></slot>
            </div>
        </div>
    </label>
</template>

<script>
    export default {
        props: ['title', 'value', 'val', 'name']
    }
</script>

<style lang="scss" scoped>
.card-input-element:checked+.category-card::before {
        content: '\f058';
        color: #72A78F;
        font-family: 'FontAwesome';
        position: absolute;
        top: 12px;
        right: 12px;
        font-size: 16px;
        padding-right: 8px;
        padding-left: 4px;
        -webkit-animation-name: fadeInCheckbox;
        animation-name: fadeInCheckbox;
        -webkit-animation-duration: .5s;
        animation-duration: .5s;
        -webkit-animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    .category-card:hover {
        background-color: #ecfef7;
    }

    .card-input-element:checked+.category-card {
        border-inline: 5px solid #72A78F;
    }

    .category-card {
        min-height: 300px;
        background-color: #fff;
        color: #223345;
        border: 1px solid #223345;
        cursor: pointer;
        user-select: none;
        border-radius: 16px;
        text-align: center;
        padding: 32px;
        position: relative;
        transition: 0.3s;

        .category-selected {
            position: absolute;
            top: 16px;
            right: 16px;
            border: 1px solid #72A78F;
            width: 24px;
            height: 24px;
            border-radius: 100%;
        }

        .category-image {
            padding: 32px;
        }

        .category-title {
            font-weight: 700;
        }

        .custom-dimantions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
        }
    }

    @-webkit-keyframes fadeInCheckbox {
        from {
            opacity: 0;
            -webkit-transform: rotateZ(-20deg);
        }

        to {
            opacity: 1;
            -webkit-transform: rotateZ(0deg);
        }
    }

    @keyframes fadeInCheckbox {
        from {
            opacity: 0;
            transform: rotateZ(-20deg);
        }

        to {
            opacity: 1;
            transform: rotateZ(0deg);
        }
    }
</style>